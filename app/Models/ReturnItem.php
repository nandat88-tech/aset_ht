<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnItem extends Model
{
    use HasFactory;

    protected $fillable = [
    'return_transaction_id',
    'handy_talky_id',
    'charger_id',
    'condition',
    'condition_note',
];

    public function returnTransaction()
    {
        return $this->belongsTo(ReturnTransaction::class);
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