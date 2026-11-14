<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

abstract class RegionalModel extends Model
{
    // A query boundary, not authorization. T07 must supply a verified, current scope.
    public function scopeForRegion(Builder $query, int $regionId): Builder
    {
        return $query->where($this->qualifyColumn('region_id'), $regionId);
    }
}
