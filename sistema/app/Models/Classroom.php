<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Classroom extends RegionalModel
{
    protected $fillable = ['region_id', 'local_unit_id', 'curriculum_edition_id', 'module_offering_id', 'level_id', 'code', 'name', 'capacity'];

    protected function casts(): array
    {
        return ['capacity' => 'integer'];
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function localUnit(): BelongsTo
    {
        return $this->belongsTo(LocalUnit::class);
    }

    public function edition(): BelongsTo
    {
        return $this->belongsTo(CurriculumEdition::class, 'curriculum_edition_id');
    }

    public function offering(): BelongsTo
    {
        return $this->belongsTo(ModuleOffering::class, 'module_offering_id');
    }

    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }
}
