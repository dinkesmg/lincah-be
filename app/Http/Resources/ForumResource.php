<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use Illuminate\Support\Str;
use Carbon\Carbon;

class ForumResource extends JsonResource
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
            'opd' => $this->user->name ?? 'Admin',
            'kategori' => $this->topik->nama ?? '-',
            'wilayah' => $this->kecamatan->nama ?? '-',
            'judul' => $this->judul,
            'hasil' => Str::limit(strip_tags($this->hasil), 100, '...'),
            'foto' => $this->foto ? url('storage/' . $this->foto) : null,
            'created_at' => $tanggal ?? Carbon::parse($this->created_at)
                ->locale('id')
                ->translatedFormat(', d F Y'),
        ];
    }
}
