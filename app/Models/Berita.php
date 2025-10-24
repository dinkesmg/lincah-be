<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;
use Illuminate\Support\Str;

class Berita extends Model implements Auditable
{
    use HasFactory, AuditableTrait;

    protected $table = 'beritas';

    protected $fillable = ["uuid", "judul", "deskripsi", "gambar", "kategori_berita_id", "tanggal"];

    protected $appends = ["gambar_url"];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = Str::uuid()->toString();
        });
    }

    public function getGambarUrlAttribute()
    {
        return $this->gambar ? asset('storage/' . $this->gambar) : null;
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriBerita::class, 'kategori_berita_id');
    }
}