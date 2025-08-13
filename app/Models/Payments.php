<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payments extends Model
{
    use HasFactory;

    public $table = 'payments';
    protected $fillable = [
        'paymentId',
        'studentId',
        'courseId',
        'courseCost',
        'amountPaid',
        'transactionReference',
        'paymentMethod',
        'paymentStatus',
        'userId',
    ];
    protected $primaryKey = 'paymentId';

     public function course()
    {
        return $this->belongsTo(Courses::class, 'courseId', 'courseId');
    }
    
     public function users()
    {
        return $this->belongsTo(User::class, 'userId', 'id');
    }
}
