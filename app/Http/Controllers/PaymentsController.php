<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\Payment;
use App\Models\CourseRegistration;
use App\Models\User;
use App\Models\Students;
use App\Models\CohortStudents;
use App\Models\Payments;
use App\Models\Courses;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\RefreshToken;    
use Carbon\Carbon;

class PaymentsController extends Controller
{
    public function index()
    {
        $payments = Payments::with('course')->where('userId', Auth::id())->get();
        return response()->json($payments);
    }

      public function allPayments()
    {
        $payments = Payments::with('users', 'course')->orderBy('created_at', 'desc')->get();
        return response()->json($payments);
    }

   
    /**
     * Handle mobile transfer payment notification
     */
  public function notifyTransfer(Request $request)
{
    // Validate the request
    $validator = Validator::make($request->all(), [
        'firstName' => 'required|string|max:255',
        'lastName' => 'required|string|max:255',
        'otherNames' => 'nullable|string|max:255',
        'gender' => 'nullable|string|max:255',
        'maritalStatus' => 'nullable|string|max:255',
        'email' => 'required|email|max:255|unique:users,email',
        'phoneNumber' => 'required|string|max:20|unique:users,phoneNumber',
        'alternatePhoneNumber' => 'nullable|string|max:255',
        'courseId' => 'required|exists:courses,courseId',
        'cohortId' => 'required|exists:cohorts,cohortId',
        'amount' => 'required|numeric|min:0',
        'password' => 'required|string|min:8',
        'transactionReference' => 'required|string|unique:payments,transactionReference',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 422);
    }

    $course = Courses::where('courseId', $request->courseId)->first();
    
    // Start database transaction
    DB::beginTransaction();

    try {
        $studentId = mt_rand(1000000, 9999999);
        
        // Check for duplicate email or phone number again (race condition protection)
        if (User::where('email', $request->email)->exists()) {
            throw new \Exception('This email address is already registered.');
        }
        
        if (User::where('phoneNumber', $request->phoneNumber)->exists()) {
            throw new \Exception('This phone number is already registered.');
        }

        // Create user record
        $user = User::create([
            'firstName' => $request->firstName,
            'lastName' => $request->lastName,
            'otherNames' => $request->otherNames,
            'email' => $request->email,
            'phoneNumber' => $request->phoneNumber,
            'password' => Hash::make($request->password),
            'role' => 1,
        ]);

        $student = Students::create([
            'userId' => $user->id,
            'studentId' => $studentId,
            'gender' => $request->gender,
            'maritalStatus' => $request->maritalStatus,
            'stateOfResidence' => $request->stateOfResidence,
            'alternatePhoneNumber' => $request->alternatePhoneNumber,
            'addedBy' => $user->id,
        ]);

        // Create payment record
        $payment = Payments::create([
            'userId' => $user->id,
            'studentId' => $studentId,
            'courseId' => $request->courseId,
            'cohortId' => $request->cohortId,
            'amountPaid' => $request->amount,
            'courseCost' => $course->cost,
            'paymentMethod' => 'bank_transfer',
            'transactionReference' => $request->transactionReference,
            'paymentStatus' => 'PENDING',
        ]);

        // Create course registration
        $registration = CohortStudents::updateOrCreate(
            [
                'userId' => $user->id,
                'cohortId' => $request->cohortId,
                'courseId' => $request->courseId,
                'studentId' => $studentId,
            ],
            [
                'enrollmentStatus' => 'pending_payment_verification',
                'enrollmentDate' => now(),
            ]
        );

        // Commit the transaction
        DB::commit();

        // Send notifications (consider queueing these)
        $this->sendAdminNotification($user, $payment);
        $this->sendWelcomeEmail($user);

        // Attempt to authenticate the user
        // MANUALLY LOGIN THE USER AND GENERATE TOKENS
        $accessToken = auth('api')->login($user);
        $refreshToken = Str::random(64);
        
        // Store refresh token
        RefreshToken::create([
            'user_id' => $user->id,
            'token' => $refreshToken,
            'expires_at' => Carbon::now()->addDays(14),
        ]);

        // Get user data (without sensitive fields)
        $authUser = auth('api')->user();
        $authUser->makeHidden(['password', 'remember_token']);

        return response()->json([
            'success' => true,
            'message' => 'Registration and payment processed successfully',
            'user' => $authUser,
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'payment_id' => $payment->id,
            'registration_id' => $registration->id,
            'role' => $user->user_role->roleName ?? '',
        ])->cookie('access_token', $accessToken, 15, null, null, true, true, false, 'strict')
        ->cookie('refresh_token', $refreshToken, 14 * 24 * 60, null, null, true, true, false, 'strict');


    } catch (\Illuminate\Database\QueryException $e) {
        DB::rollBack();
        
        $errorCode = $e->errorInfo[1];
        $errorMessage = 'Payment processing failed';
        
        // Handle specific SQL errors
        if ($errorCode == 1062) { // MySQL duplicate entry error code
            if (str_contains($e->getMessage(), 'users.email')) {
                $errorMessage = 'This email address is already registered.';
            } elseif (str_contains($e->getMessage(), 'users.phoneNumber')) {
                $errorMessage = 'This phone number is already registered.';
            } elseif (str_contains($e->getMessage(), 'payments.transactionReference')) {
                $errorMessage = 'This transaction reference has already been used.';
            }
        }

        return response()->json([
            'success' => false,
            'message' => $errorMessage,
            'error' => $e->getMessage()
        ], 400);

    } catch (\Exception $e) {
        DB::rollBack();
        
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
            'error' => $e->getMessage()
        ], 400);
    }
}

// Helper methods for notifications
private function sendAdminNotification($user, $payment)
{
    // Implement your admin notification logic here
    // Consider using Laravel notifications or events
}

private function sendWelcomeEmail($user)
{
    // Implement your welcome email logic here
}
    /**
     * Handle already paid with proof submission
     */
    public function verifyPayment(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone_number' => 'required|string|max:20',
            'course_id' => 'required|exists:courses,id',
            'cohort_id' => 'required|exists:cohorts,id',
            'amount' => 'required|numeric|min:0',
            'transaction_ref' => 'required|string|unique:payments,transaction_ref',
            'payment_proof' => 'required|file|mimes:jpeg,png,pdf|max:5120', // 5MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Store the payment proof
            $proofPath = $request->file('payment_proof')->store('payment_proofs', 'public');

            // Create payment record
            $payment = Payment::create([
                'user_id' => auth()->id() ?? null,
                'course_id' => $request->course_id,
                'cohort_id' => $request->cohort_id,
                'amount' => $request->amount,
                'payment_method' => 'already_paid',
                'transaction_ref' => $request->transaction_ref,
                'status' => 'pending_verification',
                'proof_path' => $proofPath,
                'meta' => json_encode([
                    'payer_name' => $request->first_name . ' ' . $request->last_name,
                    'payer_email' => $request->email,
                    'payer_phone' => $request->phone_number,
                ])
            ]);

            // Create course registration
            $registration = CourseRegistration::updateOrCreate(
                [
                    'user_id' => auth()->id() ?? null,
                    'email' => $request->email,
                    'course_id' => $request->course_id,
                    'cohort_id' => $request->cohort_id,
                ],
                [
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'phone_number' => $request->phone_number,
                    'payment_id' => $payment->id,
                    'status' => 'payment_pending',
                ]
            );

            // In a real application:
            // 1. Send notification to admin to verify payment
            // 2. Send confirmation email to user

            return response()->json([
                'success' => true,
                'message' => 'Payment proof submitted successfully. We will verify your payment shortly.',
                'payment_id' => $payment->id,
                'registration_id' => $registration->id,
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Payment verification failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify a payment (admin endpoint)
     */
    public function adminVerifyPayment(Request $request, $paymentId)
    {
        if (!auth()->user() || !auth()->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $payment = Payment::findOrFail($paymentId);

        // Validate the payment (in a real app, you might check with your bank API)
        $valid = $this->validatePaymentWithBank($payment);

        if ($valid) {
            $payment->update([
                'status' => 'verified',
                'verified_at' => now(),
                'verified_by' => auth()->id()
            ]);

            // Update registration status
            CourseRegistration::where('payment_id', $payment->id)
                ->update(['status' => 'enrolled']);

            // Send confirmation to user
            $this->sendEnrollmentConfirmation($payment);

            return response()->json([
                'success' => true,
                'message' => 'Payment verified successfully',
                'payment' => $payment
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Payment verification failed'
        ], 400);
    }

    /**
     * Mock bank validation (replace with actual bank API call)
     */
    private function validatePaymentWithBank(Payment $payment)
    {
        // In a real application, you would:
        // 1. For bank transfers: Call your bank's API to verify the transaction
        // 2. For already paid: Verify the uploaded proof manually or with OCR
        
        // This is just a mock implementation
        return true;
    }

    /**
     * Mock email sender (replace with actual email sending)
     */
    private function sendEnrollmentConfirmation(Payment $payment)
    {
        // Send email to user confirming enrollment
        // You would use Laravel's notification system here
    }

      public function update(Request $request)
{


    $payment = Payments::where('paymentId', $request->paymentId)->first();
    if (!$payment) {
        return response()->json(['message' => 'Payment not found'], 404);
    }

    $payment->update(['paymentStatus' => "PAID"]);
    

    return response()->json([
        'message' => "Payment updated successfully",
    ], 200);
}
}