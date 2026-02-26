<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SimkimApiService
{
    protected string $baseUrl;
    protected string $token;

    public function __construct()
    {
        $this->baseUrl = config('simkim.base_url');
        $this->token   = config('simkim.token');
    }

    /**
     * Ambil data permohonan paspor dari SIMKIM
     * berdasarkan kode permohonan
     */
    public function getPermohonanByKode(string $kodePermohonan): array
    {
        $response = Http::withHeaders([
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer ' . $this->token,
        ])->get(
            "{$this->baseUrl}/paspor/get/{$kodePermohonan}"
        );

        if ($response->failed()) {
            return [
                'success' => false,
                'message' => 'Gagal menghubungi API SIMKIM',
                'status'  => $response->status(),
            ];
        }

        return $response->json();
    }
    public function getPermohonanSelesai(): array
    {
        $response = Http::withHeaders([
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer ' . $this->token,
        ])->get("{$this->baseUrl}/paspor", [
            'status' => 'SELESAI'
        ]);

        if ($response->failed()) {
            return [];
        }

        return $response->json();
    }
}
