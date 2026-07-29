<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Charger extends Model
{
    use HasFactory;

    protected $fillable = [
        'serial_number',
        'inventory_number',
        'condition',
        'status',
        'handy_talky_id',
    ];

    // Relasi: satu charger opsional terkait ke satu HT
    public function handyTalky()
    {
        return $this->belongsTo(HandyTalky::class);
    }
}