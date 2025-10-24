<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;
use Illuminate\Support\Str;

class Forum extends Model implements Auditable
{
    use HasFactory, AuditableTrait;

    protected $table = 'forums';

    protected $fillable = ["uuid", "user_id", "foto", "judul", "hasil", "topik_id", "kecamatan_id", "rencana_tindak_lanjut", "link_dokumentasi"];

    protected $appends = ["foto_url"];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = Str::uuid()->toString();
        });
    }

    public function getFotoUrlAttribute()
    {
        return $this->foto ? asset('storage/' . $this->foto) : null;
    }

    public function topik()
    {
        return $this->belongsTo(Topik::class);
    }

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}