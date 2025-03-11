<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';

    protected $fillable = [
        'tanggal',
        'coa_kode',
        'coa_nama',
        'desc',
        'debit',
        'credit'
    ];

    public function coa()
    {
        return $this->belongsTo(COA::class, 'coa_kode', 'kode');
    }
}
