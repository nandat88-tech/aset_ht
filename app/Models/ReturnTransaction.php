<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'borrow_transaction_id',
        'return_date',
        'notes',
        'documentation_url',
        'is_late',
    ];

    protected $casts = [
        'return_date' => 'datetime',
        'is_late' => 'boolean',
    ];

    public function borrowTransaction()
    {
        return $this->belongsTo(BorrowTransaction::class);
    }

    public function items()
    {
        return $this->hasMany(ReturnItem::class);
    }
}