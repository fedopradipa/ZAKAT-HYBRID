<?php
// app/Services/PinataService.php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;

class PinataService
{
  private string $apiKey;
  private string $secretKey;
  private string $gateway;

  public function __construct()
  {
    $this->apiKey    = config('services.pinata.api_key');
    $this->secretKey = config('services.pinata.secret_key');
    $this->gateway   = config('services.pinata.gateway');
  }

  /**
   * Upload file foto bukti ke IPFS via Pinata
   */
  public function uploadFile(UploadedFile $file, string $programJudul): array
  {
    $response = Http::withHeaders([
      'pinata_api_key'        => $this->apiKey,
      'pinata_secret_api_key' => $this->secretKey,
    ])->attach(
      'file',
      file_get_contents($file->getRealPath()),
      $file->getClientOriginalName()
    )->post('https://api.pinata.cloud/pinning/pinFileToIPFS', [
      'pinataMetadata' => json_encode([
        'name' => 'bukti_' . str_replace(' ', '_', $programJudul) . '_' . now()->timestamp,
      ]),
      'pinataOptions' => json_encode([
        'cidVersion' => 1,
      ]),
    ]);

    if ($response->failed()) {
      throw new \Exception('Gagal upload ke IPFS: ' . $response->body());
    }

    $ipfsHash = $response->json('IpfsHash');

    return [
      'ipfs_hash' => $ipfsHash,
      'url'       => $this->gateway . '/' . $ipfsHash,
    ];
  }

  /**
   * ⭐ BARU: Upload Data Array (JSON) ke IPFS via Pinata
   * Digunakan untuk mengepak data Proposal & Mustahik (Terenkripsi)
   */
  public function uploadJson(array $data, string $fileName): array
  {
    $response = Http::withHeaders([
      'pinata_api_key'        => $this->apiKey,
      'pinata_secret_api_key' => $this->secretKey,
      'Content-Type'          => 'application/json',
    ])->post('https://api.pinata.cloud/pinning/pinJSONToIPFS', [
      'pinataContent'  => $data,
      'pinataMetadata' => [
        'name' => $fileName . '_' . now()->timestamp,
      ],
      'pinataOptions'  => [
        'cidVersion' => 1,
      ],
    ]);

    if ($response->failed()) {
      throw new \Exception('Gagal upload JSON ke IPFS: ' . $response->body());
    }

    $ipfsHash = $response->json('IpfsHash');

    return [
      'ipfs_hash' => $ipfsHash,
      'url'       => $this->gateway . '/' . $ipfsHash,
    ];
  }

  /**
   * Buat URL publik dari hash IPFS
   */
  public function getUrl(string $ipfsHash): string
  {
    return $this->gateway . '/' . $ipfsHash;
  }
}