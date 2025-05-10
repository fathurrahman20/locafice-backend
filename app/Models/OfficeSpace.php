<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OfficeSpace extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'thumbnail',
        'is_open',
        'is_full_booked',
        'price',
        'duration',
        'address',
        'about',
        'slug',
        'city_id'
    ];

    public function setNameAttribute($value): void
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(OfficeSpacePhoto::class);
    }

    public function benefits(): HasMany
    {
        return $this->hasMany(OfficeSpaceBenefit::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Hapus file lama saat thumbnail diupdate, dan hapus semua file saat deleting.
     */
    protected static function booted(): void
    {
        static::updating(function ($officeSpace) {
            if ($officeSpace->isDirty('thumbnail')) {
                $oldThumbnail = $officeSpace->getOriginal('thumbnail');

                if ($oldThumbnail && Storage::disk('public')->exists($oldThumbnail)) {
                    Storage::disk('public')->delete($oldThumbnail);
                }
            }
        });

        static::deleting(function ($officeSpace) {
            // Hapus thumbnail
            if ($officeSpace->thumbnail && Storage::disk('public')->exists($officeSpace->thumbnail)) {
                Storage::disk('public')->delete($officeSpace->thumbnail);
            }

            // Hapus semua photo relasi
            foreach ($officeSpace->photos as $photo) {
                if ($photo->photo && Storage::disk('public')->exists($photo->photo)) {
                    Storage::disk('public')->delete($photo->photo);
                }
            }
        });
    }
}
