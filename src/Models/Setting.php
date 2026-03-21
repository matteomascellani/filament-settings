<?php

namespace Matteomascellani\FilamentSettings\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Setting extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $table = 'settings';

    protected $fillable = [
        'group',
        'key',
        'label',
        'description',
        'value_type',
        'is_active',
        'sort',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function registerMediaCollections(): void
    {
        $publicDisk = config('media-library.collection_disks.public', 'public');

        $this->addMediaCollection('media_value')
            ->useDisk($publicDisk)
            ->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        // No conversions needed for logo uploads.
    }

    public function values(): HasMany
    {
        return $this->hasMany(SettingValue::class)
            ->orderBy('sort')
            ->orderBy('id');
    }
}
