<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Expense extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'category_id',
        'description',
        'amount',
        'iban',
        'status',
        'rejection_reason',
        'attachment_path',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
