<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DataSpasialResource extends JsonResource
{
    public function __construct($resource)
    {
        parent::__construct($resource);
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request)
    {
        $zona = 'Tidak Ada Kasus';
        $warna = '#CCCCCC';

        if ($this->jumlah_kasus > 0) {
            switch (true) {
                case $this->jumlah_kasus <= 7:
                    $zona = 'Sangat Rendah';
                    $warna = '#3B82F6';
                    break;
                case $this->jumlah_kasus <= 20:
                    $zona = 'Rendah';
                    $warna = '#6EE7B7';
                    break;
                case $this->jumlah_kasus <= 35:
                    $zona = 'Rendah - Sedang';
                    $warna = '#FDE68A';
                    break;
                case $this->jumlah_kasus <= 41:
                    $zona = 'Sedang';
                    $warna = '#FEF9C3';
                    break;
                case $this->jumlah_kasus <= 50:
                    $zona = 'Sedang - Tinggi';
                    $warna = '#FECACA';
                    break;
                case $this->jumlah_kasus <= 60:
                    $zona = 'Tinggi';
                    $warna = '#FB923C';
                    break;
                default:
                    $zona = 'Sangat Tinggi';
                    $warna = '#EF4444';
                    break;
            }
        }

        $koordinat = [0, 0];
        if (!empty($this->kelurahan->koordinat)) {
            $parts = explode(',', $this->kelurahan->koordinat);
            if (count($parts) == 2) {
                $koordinat = [floatval(trim($parts[0])), floatval(trim($parts[1]))];
            }
        }

        $response = [
            'id' => $this->id,
            'kecamatan' => $this->kecamatan->nama ?? '-',
            'kelurahan' => $this->kelurahan->nama ?? '-',
            'tahun' => $this->tahun,
            'jenis_risiko' => $this->jenisKasus ?? '-',
            'zona' => $zona,
            'warna' => $warna,
            'koordinat' => $koordinat,
        ];

        if (isset($this->kerentanan)) {
            $response['kerentanan'] = (int) ($this->kerentanan ?? 0);
        }

        if (isset($this->keterpaparan)) {
            $response['keterpaparan'] = (int) ($this->keterpaparan ?? 0);
        }

        if (isset($this->potensial_dampak)) {
            $response['potensial_dampak'] = (int) ($this->potensial_dampak ?? 0);
        }

        if (isset($this->jumlah_kasus)) {
            $response['jumlah_kasus'] = (int) ($this->jumlah_kasus ?? 0);
        }

        return $response;
    }
}
