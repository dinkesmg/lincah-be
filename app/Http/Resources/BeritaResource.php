<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use Illuminate\Support\Str;
use Carbon\Carbon;

class BeritaResource extends JsonResource
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

        if ($this->tanggal) {
            $tanggal = Carbon::parse($this->tanggal)
                ->locale('id')
                ->translatedFormat(', d F Y');
        }
        
        return [
            'id' => $this->id,
            'user' => 'Admin',
            'kategori' => $this->kategori->nama ?? '-',
            'judul' => $this->judul,
            'gambar' => $this->gambar ? url('storage/' . $this->gambar) : null,
            'created_at' => $tanggal,
        ];
    }
}
