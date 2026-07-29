<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BorrowItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'borrow_transaction_id',
        'handy_talky_id',
        'charger_id',
    ];

    public function borrowTransaction()
    {
        return $this->belongsTo(BorrowTransaction::class);
    }

    public function handyTalky()
    {
        return $this->belongsTo(HandyTalky::class);
    }

    public function charger()
    {
        return $this->belongsTo(Charger::class);
    }
}