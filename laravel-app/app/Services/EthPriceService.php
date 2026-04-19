<?php
// app/Services/EthPriceService.php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EthPriceService
{
    /**
     * Ambil harga ETH dalam IDR dari CoinGecko
     * Cache 5 menit agar tidak spam API
     */
    public function getEthToIdr(): float
    {
        return Cache::remember('eth_price_idr', 300, function () {
            try {
                $response = Http::timeout(5)
                    ->get('https://api.coingecko.com/api/v3/simple/price', [
                        'ids'           => 'ethereum',
                        'vs_currencies' => 'idr',
                    ]);

                if ($response->ok()) {
                    return (float) $response->json('ethereum.idr');
                }

                Log::warning('CoinGecko API gagal, pakai fallback harga.');
                return $this->getFallbackPrice();

            } catch (\Exception $e) {
                Log::warning('CoinGecko timeout: ' . $e->getMessage());
                return $this->getFallbackPrice();
            }
        });
    }

    /**
     * Fallback jika API tidak bisa diakses
     * Update manual sesuai harga terakhir yang diketahui
     */
    private function getFallbackPrice(): float
    {
        return 50_000_000;
    }
}