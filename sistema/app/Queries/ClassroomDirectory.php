<?php

namespace App\Queries;

use App\Models\Classroom;
use App\Models\Region;
use Illuminate\Database\Eloquent\Collection;

final class ClassroomDirectory
{
    // Region is an explicit query boundary. It will come from verified assignments in T07.
    public function search(Region $region, ?int $unitId = null, string $term = ''): Collection
    {
        if ($unitId !== null) {
            $region->localUnits()->findOrFail($unitId);
        }
        $literal = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], trim($term));

        return Classroom::forRegion($region->id)
            ->when($unitId !== null, fn ($query) => $query->where('local_unit_id', $unitId))
            ->when($literal !== '', fn ($query) => $query->where('name', 'ilike', '%'.$literal.'%'))
            ->with(['region', 'localUnit', 'level', 'offering.module', 'edition'])
            ->orderBy('local_unit_id')->orderBy('name')->orderBy('id')
            ->limit(100)->get();
    }
}
