<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;
use Illuminate\Support\Str;

class Video extends Model implements Auditable
{
    use HasFactory, AuditableTrait;

    protected $table = 'videos';

    protected $fillable = ["uuid", "link"];

    protected $appends = ["thumbnail_url"];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = Str::uuid()->toString();
        });
    }
}