<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentUpload extends Model
{
    protected $table = 'documents';
    protected $fillable = [
        'documentId',
        'type',
        'studentId',
        'url',
        'status',
    ];
    protected $primaryKey = 'documentId';

    public function product()
    {
        return $this->belongsTo(Product::class, 'productId', 'productId');
    }
}
