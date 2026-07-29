<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BorrowTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'borrow_date',
        'due_date',
        'notes',
        'document_url',
        'status',
    ];

    protected $casts = [
        'borrow_date' => 'datetime',
        'due_date' => 'date',
    ];

    // Relasi: satu transaksi dimiliki oleh satu pegawai (peminjam)
    public function employee()
    {
        return $this->belongsTo(Employee::class);
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