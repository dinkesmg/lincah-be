<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;
use Illuminate\Support\Str;

class DataRt extends Model implements Auditable
{
    use HasFactory, AuditableTrait;

    protected $table = 'data_rts';

    protected $fillable = ["uuid", "bulan", "tahun", "kecamatan_id", "kelurahan_id", "rw_id", "rt_id", "jenis_kasus_id", "keterpaparan", "kerentanan", "potensial_dampak", "jumlah_kasus", "image"];

    

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = Str::uuid()->toString();
        });
    }

    public function scopeByUserRole(Builder $query)
    {
        $user = auth()->user();
        $role = $user->getRole()->name;

        if (!$user) {
            return $query->whereRaw('1 = 0');
        }

        if ($role === 'PUSKESMAS') {
            $wilayah = DB::table('user_wilayah')
                ->where('users_id', $user->id)
                ->select('kecamatan_id', 'kelurahan_id')
                ->get();

            $kecamatanIds = $wilayah->pluck('kecamatan_id')->filter()->unique()->toArray();
            $kelurahanIds = $wilayah->pluck('kelurahan_id')->filter()->unique()->toArray();

            $query->where(function ($q) use ($kecamatanIds, $kelurahanIds) {
                $q->whereIn('kelurahan_id', $kelurahanIds);
                $q->orWhereIn('kecamatan_id', $kecamatanIds);
            });
        }

        return $query;
    }

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class);
    }

    public function kelurahan()
    {
        return $this->belongsTo(Kelurahan::class);
    }

    public function rw()
    {
        return $this->belongsTo(RW::class);
    }

    public function rt()
    {
        return $this->belongsTo(RT::class);
    }

    public function jenisKasus()
    {
        return $this->belongsTo(JenisKasus::class);
    }

    
}