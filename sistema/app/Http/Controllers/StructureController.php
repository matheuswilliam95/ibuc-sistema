<?php

namespace App\Http\Controllers;

use App\Actions\Structure\CreateStructureRecord;
use App\Models\CurriculumEdition;
use App\Models\CurriculumModule;
use App\Models\Level;
use App\Models\ModuleOffering;
use App\Models\PedagogicalRule;
use App\Models\Region;
use App\Queries\ClassroomDirectory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

final class StructureController extends Controller
{
    public function index(Request $request, ClassroomDirectory $directory): View
    {
        $filters = $request->validate([
            'region' => ['nullable', 'integer', 'exists:regions,id'],
            'unit' => ['nullable', 'integer'], 'q' => ['nullable', 'string', 'max:120'],
            'form' => ['nullable', Rule::in(CreateStructureRecord::TYPES)],
        ]);
        $regions = Region::orderBy('name')->get();
        $region = isset($filters['region']) ? $regions->firstWhere('id', (int) $filters['region']) : $regions->first();
        $units = $region?->localUnits()->orderBy('name')->get() ?? collect();
        $unitId = isset($filters['unit']) ? (int) $filters['unit'] : null;
        if ($unitId !== null) {
            abort_unless($region && $units->contains('id', $unitId), 404);
        }
        $editions = $region ? CurriculumEdition::forRegion($region->id)->with(['levels' => fn ($q) => $q->orderBy('position'), 'modules' => fn ($q) => $q->orderBy('position'), 'rules'])->orderBy('id')->get() : collect();

        return view('structure.index', [
            'regions' => $regions, 'region' => $region, 'units' => $units, 'editions' => $editions,
            'modules' => $region ? CurriculumModule::forRegion($region->id)->with('edition')->orderBy('position')->get() : collect(),
            'rules' => $region ? PedagogicalRule::forRegion($region->id)->with('edition')->orderBy('version')->get() : collect(),
            'levels' => $region ? Level::forRegion($region->id)->with('edition')->orderBy('position')->get() : collect(),
            'offerings' => $region ? ModuleOffering::forRegion($region->id)->with(['localUnit', 'module', 'edition'])->orderBy('starts_on')->get() : collect(),
            'classrooms' => $region ? $directory->search($region, $unitId, $filters['q'] ?? '') : collect(),
            'filters' => $filters, 'formType' => $filters['form'] ?? ($region ? 'classroom' : 'region'),
        ]);
    }

    public function store(Request $request, string $type, CreateStructureRecord $create): RedirectResponse
    {
        $request->validate(['region_id' => ['nullable', 'integer']]);
        $region = $request->filled('region_id') ? Region::findOrFail($request->integer('region_id')) : null;
        $record = $create->handle($type, $region, $request->all());

        return redirect()->route('structure.index', ['region' => $type === 'region' ? $record->id : $region->id, 'form' => $type])
            ->with('status', 'Cadastro salvo no banco local. A configuração pedagógica permanece em rascunho.');
    }
}
