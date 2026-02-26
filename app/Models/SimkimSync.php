<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SimkimSync extends Model
{
    protected $table = 'simkim_syncs';

    protected $fillable = [
        'kode_permohonan',
        'data_snapshot',
        'status_proses',
        'sudah_dikirim',
        'synced_at',
    ];

    protected $casts = [
        'data_snapshot' => 'array',
        'sudah_dikirim' => 'boolean',
        'synced_at'     => 'datetime',
    ];
}