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
    private const PAGES = [
        'organization' => ['structure.index', 'Organização'],
        'units' => ['structure.units', 'Núcleos'],
        'classrooms' => ['structure.classrooms', 'Turmas'],
        'offerings' => ['structure.offerings', 'Ofertas'],
        'curriculum' => ['structure.curriculum', 'Currículo'],
        'create' => ['structure.create.index', 'Novo cadastro'],
    ];

    private const FORM_LABELS = ['region' => 'Regional', 'unit' => 'Núcleo', 'edition' => 'Edição', 'level' => 'Nível', 'module' => 'Módulo', 'rule' => 'Regulamento', 'offering' => 'Oferta', 'classroom' => 'Turma'];

    public function index(Request $request): View|RedirectResponse
    {
        $data = $this->context($request, 'organization');
        // Preserve bookmarks from the original combined screen.
        if ($request->filled('form')) {
            $legacy = $request->validate(['form' => ['required', Rule::in(CreateStructureRecord::TYPES)]]);

            return redirect()->route('structure.create', ['type' => $legacy['form'], 'region' => $data['region']?->id]);
        }
        if ($request->hasAny(['q', 'unit'])) {
            return redirect()->route('structure.classrooms', $data['filters']);
        }

        return view('structure.overview', $data + ['counts' => [
            'units' => $data['units']->count(),
            'classrooms' => $data['region']?->classrooms()->count() ?? 0,
            'offerings' => $data['region'] ? ModuleOffering::forRegion($data['region']->id)->count() : 0,
            'curriculum' => $data['region']?->editions()->count() ?? 0,
        ]]);
    }

    public function units(Request $request): View
    {
        return view('structure.units', $this->context($request, 'units'));
    }

    public function classrooms(Request $request, ClassroomDirectory $directory): View
    {
        $data = $this->context($request, 'classrooms');

        return view('structure.classrooms', $data + [
            'classrooms' => $data['region'] ? $directory->search($data['region'], $data['filters']['unit'] ?? null, $data['filters']['q'] ?? '') : collect(),
        ]);
    }

    public function offerings(Request $request): View
    {
        $data = $this->context($request, 'offerings');

        return view('structure.offerings', $data + [
            'offerings' => $data['region'] ? ModuleOffering::forRegion($data['region']->id)
                ->when(isset($data['filters']['unit']), fn ($query) => $query->where('local_unit_id', $data['filters']['unit']))
                ->with(['localUnit', 'module', 'edition', 'rule'])->orderBy('starts_on')->orderBy('id')->get() : collect(),
        ]);
    }

    public function curriculum(Request $request): View
    {
        $data = $this->context($request, 'curriculum');

        return view('structure.curriculum', $data + [
            'editions' => $data['region'] ? CurriculumEdition::forRegion($data['region']->id)
                ->with(['levels' => fn ($q) => $q->orderBy('position'), 'modules' => fn ($q) => $q->orderBy('position'), 'rules'])
                ->orderBy('id')->get() : collect(),
        ]);
    }

    public function createIndex(Request $request): View
    {
        $back = $request->validate(['back' => ['nullable', Rule::in(array_keys(self::PAGES))]])['back'] ?? 'create';

        return view('structure.create-index', $this->context($request, 'create') + ['formLabels' => self::FORM_LABELS, 'backPage' => $back]);
    }

    public function create(Request $request, string $type): View
    {
        abort_unless(in_array($type, CreateStructureRecord::TYPES, true), 404);
        $data = $this->context($request, 'create');
        $regionId = $data['region']?->id;
        $back = $request->validate(['back' => ['nullable', Rule::in(array_keys(self::PAGES))]])['back'] ?? $this->destination($type);
        $titles = ['region' => 'Nova regional', 'unit' => 'Novo núcleo', 'edition' => 'Nova edição', 'level' => 'Novo nível', 'module' => 'Novo módulo', 'rule' => 'Novo regulamento', 'offering' => 'Nova oferta', 'classroom' => 'Nova turma'];

        return view('structure.create', array_replace($data, [
            'structurePageTitle' => $titles[$type], 'contextRoute' => 'structure.create', 'contextParameters' => ['type' => $type],
            'formType' => $type, 'formLabels' => self::FORM_LABELS,
            'backUrl' => route(self::PAGES[$back][0], ['region' => $regionId] + array_intersect_key($data['filters'], array_flip(['unit', 'q']))),
            'editions' => $regionId && in_array($type, ['level', 'module', 'rule', 'offering'], true) ? CurriculumEdition::forRegion($regionId)->orderBy('id')->get() : collect(),
            'modules' => $regionId && $type === 'offering' ? CurriculumModule::forRegion($regionId)->with('edition')->orderBy('position')->get() : collect(),
            'rules' => $regionId && $type === 'offering' ? PedagogicalRule::forRegion($regionId)->with('edition')->orderBy('version')->get() : collect(),
            'levels' => $regionId && $type === 'classroom' ? Level::forRegion($regionId)->with('edition')->orderBy('position')->get() : collect(),
            'offerings' => $regionId && $type === 'classroom' ? ModuleOffering::forRegion($regionId)->with(['localUnit', 'module', 'edition'])->orderBy('starts_on')->get() : collect(),
        ]));
    }

    public function store(Request $request, string $type, CreateStructureRecord $create): RedirectResponse
    {
        $request->validate(['region_id' => ['nullable', 'integer']]);
        $region = $request->filled('region_id') ? Region::findOrFail($request->integer('region_id')) : null;
        $record = $create->handle($type, $region, $request->all());

        return redirect()->route(self::PAGES[$this->destination($type)][0], ['region' => $type === 'region' ? $record->id : $region->id])
            ->with('status', 'Cadastro salvo no banco local. A configuração pedagógica permanece em rascunho.');
    }

    private function destination(string $type): string
    {
        return match ($type) {
            'region' => 'organization', 'unit' => 'units', 'classroom' => 'classrooms', 'offering' => 'offerings',
            default => 'curriculum',
        };
    }

    private function context(Request $request, string $page): array
    {
        $filters = $request->validate([
            'region' => ['nullable', 'integer', 'exists:regions,id'],
            'unit' => ['nullable', 'integer'], 'q' => ['nullable', 'string', 'max:120'],
        ]);
        if (isset($filters['unit'])) {
            $filters['unit'] = (int) $filters['unit'];
        }
        $regions = Region::orderBy('name')->get();
        $region = isset($filters['region']) ? $regions->firstWhere('id', (int) $filters['region']) : $regions->first();
        $units = $region?->localUnits()->orderBy('name')->get() ?? collect();
        abort_if(isset($filters['unit']) && ! $units->contains('id', $filters['unit']), 404);

        return [
            'regions' => $regions, 'region' => $region, 'units' => $units, 'filters' => $filters,
            'pageKey' => $page, 'structurePageTitle' => self::PAGES[$page][1],
            'contextRoute' => self::PAGES[$page][0], 'contextParameters' => [],
        ];
    }
}
