<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;
use Illuminate\Support\Str;

class Kelurahan extends Model implements Auditable
{
    use HasFactory, AuditableTrait;

    protected $table = 'kelurahans';

    protected $fillable = ["uuid", "kecamatan_id", "nama", "koordinat"];

    

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = Str::uuid()->toString();
        });
    }

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class);
    }
    
}