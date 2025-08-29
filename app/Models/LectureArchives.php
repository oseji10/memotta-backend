<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Farmers extends Model
{
    use HasFactory;

    public $table = 'lecturer_archives';
    protected $fillable = [
        'id',
        'title',
        'url',
        'date',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    // public function states()
    // {
    //     return $this->belongsTo(State::class, 'state', 'stateId');
    // } 

     public function msp()
    {
        return $this->belongsTo(MSPs::class, 'msp', 'mspId');
    } 

   
}
