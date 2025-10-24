<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;
use Illuminate\Support\Str;

class Foto extends Model implements Auditable
{
    use HasFactory, AuditableTrait;

    protected $table = 'fotos';

    protected $fillable = ["uuid", "file"];

    protected $appends = ["file_url"];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = Str::uuid()->toString();
        });
    }

        public function getFileUrlAttribute()
    {
        return $this->file ? asset('storage/' . $this->file) : null;
    }
}