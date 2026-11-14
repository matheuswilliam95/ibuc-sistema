<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PedagogicalRule extends RegionalModel
{
    protected $fillable = ['region_id', 'curriculum_edition_id', 'version', 'cutoff_policy', 'transition_policy', 'leap_day_policy', 'valid_from', 'valid_until'];

    protected function casts(): array
    {
        return ['version' => 'integer', 'valid_from' => 'immutable_date', 'valid_until' => 'immutable_date'];
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
