<?php

namespace App\Imports;

use App\Models\Data;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DataImport implements ToModel, WithHeadingRow
{
    protected $jenisKasusId;

    public function __construct($jenisKasusId)
    {
        $this->jenisKasusId = $jenisKasusId;
    }

    public function model(array $row)
    {
        return new Data([
            'uuid'              => Str::uuid(),
            'bulan'             => $row['bulan'],
            'tahun'             => $row['tahun'],
            'kecamatan_id'      => $row['kecamatan_id'],
            'kelurahan_id'      => $row['kelurahan_id'],
            'jenis_kasus_id'    => $this->jenisKasusId,
            'keterpaparan'      => $row['keterpaparan'],
            'kerentanan'        => $row['kerentanan'],
            'potensial_dampak'  => $row['potensial_dampak'],
            'jumlah_kasus'      => $row['jumlah_kasus'],
        ]);
    }
}
