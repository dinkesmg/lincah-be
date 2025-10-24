<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use Carbon\Carbon;

class BeritaDetailResource extends JsonResource
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
        $tanggal = null;

        if ($this->created_at) {
            $tanggal = Carbon::parse($this->created_at)
                ->locale('id')
                ->translatedFormat(', d F Y');
        }
        
        return [
            'id' => $this->id,
            'user' => 'Admin',
            'kategori' => $this->kategori->nama ?? '-',
            'judul' => $this->judul,
            'deskripsi' => $this->deskripsi,
            'gambar' => $this->gambar ? url('storage/' . $this->gambar) : null,
            'created_at' => $tanggal,
        ];
    }
}
