<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HandyTalky extends Model
{
    use HasFactory;

    protected $fillable = [
        'serial_number',
        'inventory_number',
        'brand',
        'model',
        'frequency',
        'photo_url',
        'condition',
        'status',
        'purchase_date',
        'location_id',
    ];

    protected $casts = [
        'purchase_date' => 'date',
    ];

    // Relasi: satu HT dimiliki oleh satu lokasi
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function chargers()
    {
        return $this->hasMany(Charger::class);
    }
}