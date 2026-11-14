<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CurriculumEdition extends RegionalModel
{
    protected $fillable = ['region_id', 'code', 'version', 'name', 'source_reference'];

    protected function casts(): array
    {
        return ['version' => 'integer'];
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function levels(): HasMany
    {
        return $this->hasMany(Level::class);
    }

    public function modules(): HasMany
    {
        return $this->hasMany(CurriculumModule::class);
    }

    public function rules(): HasMany
    {
        return $this->hasMany(PedagogicalRule::class);
    }
}
