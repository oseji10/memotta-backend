<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Students extends Model
{
    use HasFactory;

    public $table = 'students';
    protected $fillable = [
        'studentId',
        'gender',
        'maritalStatus',
        'alternantePhoneNumber',
        'stateOfResidence',
        'userId',
        'addedBy',
        'status',
    ];
    protected $primaryKey = 'studentId';
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    public function states()
    {
        return $this->belongsTo(State::class, 'state', 'stateId');
    } 

     public function lgas()
    {
        return $this->belongsTo(Lgas::class, 'lga', 'lgaId');
    } 

 public function subhubs()
    {
        return $this->hasMany(Subhubs::class, 'hubId', 'hubId');
    } 

     public function added_by()
    {
        return $this->belongsTo(User::class, 'addedBy', 'id');
    } 

     public function hub()
    {
        return $this->belongsTo(Hubs::class, 'hubId', 'hubId');
    } 
}
