<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Payment extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'expense_id',
        'status',
    ];

    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }
}
