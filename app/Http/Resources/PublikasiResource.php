<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PublikasiResource extends JsonResource
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
        $videoId = null;

        if ($this->tipe == 'video') {
            preg_match('/(?:youtube\.com\/.*v=|youtu\.be\/)([A-Za-z0-9_-]+)/', $this->link, $matches);
            $videoId = $matches[1] ?? null;
        }

        return [
            'id' => $this->id,
            'tipe' => $this->tipe,
            'foto' => $this->foto
                ? url('storage/' . $this->foto)
                : ($videoId ? "https://img.youtube.com/vi/{$videoId}/hqdefault.jpg" : null),
            'link' => $videoId
                ? "https://www.youtube.com/watch?v={$videoId}"
                : ($this->link ?? null),
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i') : null,
        ];
    }
    
}
