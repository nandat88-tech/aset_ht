<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BorrowTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'destination_location_id',
        'borrow_date',
        'due_date',
        'notes',
        'purpose',
        'document_url',
        'status',
    ];

    protected $casts = [
        'borrow_date' => 'datetime',
        'due_date' => 'date',
        'last_reminder_sent_at' => 'datetime',
    ];

    // Relasi: satu transaksi dimiliki oleh satu pegawai (peminjam)
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    public function destinationLocation()
{
    return $this->belongsTo(\App\Models\Location::class, 'destination_location_id');
}

    // Relasi: satu transaksi punya banyak item (HT/Charger yang dipinjam)
    public function items()
    {
        return $this->hasMany(BorrowItem::class);
    }

    public function returnTransaction()
    {
        return $this->hasOne(ReturnTransaction::class);
    }
}
