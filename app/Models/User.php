<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

use Illuminate\Foundation\Auth\User as Authenticatable;

 use HasRoles;
class User extends Authenticatable implements JWTSubject
{
    use HasFactory;
    use SoftDeletes;
    use HasApiTokens, Notifiable;

    public $table = 'users';
    protected $fillable = [
        'phoneNumber',
        'email',
        'role',
        'firstName',
        'lastName',
        'otherNames',
        'password', 
    ];
    protected $dates = ['deleted_at'];
    protected $hidden = ['password'];


    public function getJWTIdentifier()
    {
        return $this->getKey(); // Returns the user's primary key (e.g., ID)
    }

    public function getJWTCustomClaims()
    {
        return [
            'role' => $this->role, // Add custom claims, e.g., role (pharmacist, doctor, etc.)
        ];
    }

    
    public function user_role()
    {
        return $this->belongsTo(Role::class, 'role', 'roleId'); 
    }

       public function student_data()
    {
        return $this->hasOne(Students::class, 'userId', 'id');
    }

    public function hospital_admins()
    {
        return $this->belongsTo(HospitalStaff::class, 'id', 'userId'); 
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'userId', 'id'); 
    }

      public function applications()
    {
        return $this->belongsTo(Applications::class, 'userId', 'id');
    }
     

      public function application_type()
    {
        return $this->belongsTo(ApplicationType::class, 'applicationType', 'typeId');
    }


     public function role_details()
    {
        return $this->belongsTo(Role::class, 'role', 'roleId');
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole($roleName)
    {
        // If passed an array of roles, check if user has any of them
        if (is_array($roleName)) {
            return in_array($this->role_details->roleName, $roleName);
        }
        
        // Check for a specific role
        return $this->role_details->roleName === $roleName;
    }

    /**
     * Check if user is an admin
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('ADMIN');
    }

    /**
     * Check if user is an instructor
     */
    public function isInstructor(): bool
    {
        return $this->hasRole('INSTRUCTOR');
    }

    /**
     * Check if user is a student
     */
    public function isStudent(): bool
    {
        return $this->hasRole('STUDENT');
    }

}
