<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Region extends Model
{
    protected $fillable = ['code', 'name', 'timezone'];

    public function localUnits(): HasMany
    {
        return $this->hasMany(LocalUnit::class);
    }

    public function editions(): HasMany
    {
        return $this->hasMany(CurriculumEdition::class);
    }

    public function classrooms(): HasMany
    {
        return $this->hasMany(Classroom::class);
    }
}
