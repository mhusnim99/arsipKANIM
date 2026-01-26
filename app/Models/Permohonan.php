<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Permohonan extends Model
{
    use HasFactory;

    protected $table = 'permohonan'; // Pastikan nama tabel sesuai

    protected $fillable = [
        'nomor_permohonan',
        'nama_pemohon',
        'nomor_paspor',
        'tanggal',
        'status',
        'lemari',
        'loker',
        'nomor_arsip',
        'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    /**
     * Scope untuk pencarian
     */
    public function scopeSearch($query, array $filters)
    {
        return $query->when($filters['nomor_permohonan'] ?? false, function ($query, $nomor) {
            $query->where('nomor_permohonan', 'like', '%' . $nomor . '%');
        })
        ->when($filters['nomor_paspor'] ?? false, function ($query, $paspor) {
            $query->where('nomor_paspor', 'like', '%' . $paspor . '%');
        })
        ->when($filters['nama_pemohon'] ?? false, function ($query, $nama) {
            $query->where('nama_pemohon', 'like', '%' . $nama . '%');
        })
        ->when($filters['tanggal'] ?? false, function ($query, $tanggal) {
            try {
                $date = \Carbon\Carbon::createFromFormat('d/m/Y', $tanggal)->format('Y-m-d');
                $query->whereDate('tanggal', $date);
            } catch (\Exception $e) {
                // Tanggal invalid, skip filter ini
            }
        });
    }
}
