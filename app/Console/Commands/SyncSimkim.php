<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SimkimApiService;
use App\Models\PengirimanBerkas;
use Illuminate\Support\Facades\Log;

class SyncSimkim extends Command
{
    protected $signature = 'simkim:sync';
    protected $description = 'Recheck status permohonan menunggu dari SIMKIM';

    protected $simkimService;

    public function __construct(SimkimApiService $simkimService)
    {
        parent::__construct();
        $this->simkimService = $simkimService;
    }

    public function handle()
    {
        $this->info('Mulai recheck status SIMKIM...');

        $pengirimanList = PengirimanBerkas::where('status', 'menunggu')
            ->select('id', 'kode_permohonan', 'simkim_snapshot')
            ->get();

        if ($pengirimanList->isEmpty()) {
            $this->info('Tidak ada data menunggu untuk dicek.');
            return Command::SUCCESS;
        }

        foreach ($pengirimanList as $pengiriman) {

            try {

                $result = $this->simkimService
                    ->getPermohonanByKode($pengiriman->kode_permohonan);

                if (!($result['success'] ?? false)) {
                    continue;
                }

                $snapshotBaru = $result['data'];

                $statusBaru = $snapshotBaru['permohonan']['alurterakhir'] ?? null;

                $statusLama = $pengiriman->simkim_snapshot['permohonan']['alurterakhir'] ?? null;

                if ($statusBaru && $statusBaru !== $statusLama) {

                    $pengiriman->update([
                        'simkim_snapshot' => $snapshotBaru,
                        'updated_at'      => now()
                    ]);

                    $this->info("Status berubah untuk {$pengiriman->kode_permohonan} ({$statusLama} → {$statusBaru})");
                }

            } catch (\Throwable $e) {
                Log::error("Recheck gagal {$pengiriman->kode_permohonan}: " . $e->getMessage());
            }
        }

        $this->info('Recheck selesai.');
        return Command::SUCCESS;
    }
}