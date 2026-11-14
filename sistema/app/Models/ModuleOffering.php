<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ModuleOffering extends RegionalModel
{
    protected $fillable = ['region_id', 'local_unit_id', 'curriculum_edition_id', 'curriculum_module_id', 'pedagogical_rule_id', 'code', 'name', 'starts_on', 'ends_on', 'timezone', 'capacity'];

    protected function casts(): array
    {
        return ['starts_on' => 'immutable_date', 'ends_on' => 'immutable_date', 'actual_started_at' => 'immutable_datetime', 'capacity' => 'integer'];
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

    public function module(): BelongsTo
    {
        return $this->belongsTo(CurriculumModule::class, 'curriculum_module_id');
    }

    public function rule(): BelongsTo
    {
        return $this->belongsTo(PedagogicalRule::class, 'pedagogical_rule_id');
    }

    public function classrooms(): HasMany
    {
        return $this->hasMany(Classroom::class);
    }
}
