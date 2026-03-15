<?php

namespace Matteomascellani\FilamentSettings\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SettingValue extends Model
{
    use HasFactory;

    protected $table = 'setting_values';

    protected $fillable = [
        'setting_id',
        'name',
        'value',
        'sort',
        'settable_type',
        'settable_id',
    ];

    public function setting(): BelongsTo
    {
        return $this->belongsTo(Setting::class);
    }

    public function settable(): MorphTo
    {
        return $this->morphTo();
    }
}
