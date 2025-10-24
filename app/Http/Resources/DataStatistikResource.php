<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DataStatistikResource extends JsonResource
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
        $response = [
            'bulan' => $this->bulan,
            'nama_bulan' => $this->nama_bulan,
            'tahun' => $this->tahun,
            'jenis_risiko' => $this->jenisKasus ?? '-',
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
