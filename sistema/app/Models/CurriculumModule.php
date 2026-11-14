<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CurriculumModule extends RegionalModel
{
    protected $fillable = ['region_id', 'curriculum_edition_id', 'code', 'name', 'position', 'lesson_count'];

    protected function casts(): array
    {
        return ['position' => 'integer', 'lesson_count' => 'integer'];
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function edition(): BelongsTo
    {
        return $this->belongsTo(CurriculumEdition::class, 'curriculum_edition_id');
    }
}
