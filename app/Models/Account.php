<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Tymon\JWTAuth\Facades\JWTAuth;

class Account extends Model implements JWTSubject
{
    protected $guarded = ['id'];
    protected $appends = ['language'];
    protected $casts = ['settings' => 'array'];

    use HasFactory, HasUuids, SoftDeletes;

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function mediaFiles()
    {
        return $this->hasMany(MediaFile::class);
    }

    public function productUsage(): HasMany
    {
        return $this->hasMany(ProductUsage::class);
    }

    public function unitTransactions(): HasMany
    {
        return $this->hasMany(UnitTransaction::class);
    }

    public function getLanguageAttribute()
    {
        return $this->settings['language'] ?? 'en';
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function getJWTToken()
    {
        return JWTAuth::fromUser($this);
    }

    public function registerUnits(int $amount, array $meta = []): UnitTransaction
    {
        return $this->unitTransactions()->create([
            'amount' => $amount,
            'meta' => $meta
        ]);
    }

    public static function newFactory()
    {
        return \Database\Factories\AccountFactory::new();
    }
}
