<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TabelDataResource extends JsonResource
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

        return [
            'id' => $this->id,
            'kecamatan' => $this->kecamatan->nama ?? '-',
            'kelurahan' => $this->kelurahan->nama ?? '-',
            'tahun' => $this->tahun,
            'jenis_risiko' => $this->jenisKasus ?? '-',
            'jumlah_kasus' => $this->jumlah_kasus,
            'keterpaparan' => $this->keterpaparan,
            'kerentanan' => $this->kerentanan,
            'potensial_dampak' => $this->potensial_dampak,
            'zona' => $zona,
        ];
    }
}
