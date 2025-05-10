<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class City extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'photo'
    ];

    public function setNameAttribute($value): void
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }

    public function officeSpaces(): HasMany
    {
        return $this->hasMany(OfficeSpace::class);
    }

    protected static function booted(): void
    {
        static::updating(function ($cityPhoto) {
            if ($cityPhoto->isDirty('photo')) {
                $oldPhoto = $cityPhoto->getOriginal('photo');

                if ($oldPhoto && Storage::disk('public')->exists($oldPhoto)) {
                    Storage::disk('public')->delete($oldPhoto);
                }
            }
        });

        static::deleting(function ($cityPhoto) {
            // Hapus foto city
            if ($cityPhoto->photo && Storage::disk('public')->exists($cityPhoto->photo)) {
                Storage::disk('public')->delete($cityPhoto->photo);
            }

            // Hapus semua photo relasi
            if ($cityPhoto->photo && Storage::disk('public')->exists($cityPhoto->photo)) {
                Storage::disk('public')->delete($cityPhoto->photo);
            }
        });
    }
}
