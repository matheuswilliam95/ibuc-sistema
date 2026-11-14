<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Level extends RegionalModel
{
    protected $fillable = ['region_id', 'curriculum_edition_id', 'code', 'name', 'position', 'min_age', 'max_age'];

    protected function casts(): array
    {
        return ['position' => 'integer', 'min_age' => 'integer', 'max_age' => 'integer'];
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
