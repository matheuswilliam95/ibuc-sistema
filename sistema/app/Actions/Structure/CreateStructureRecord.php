<?php

namespace App\Actions\Structure;

use App\Models\Classroom;
use App\Models\CurriculumEdition;
use App\Models\CurriculumModule;
use App\Models\Level;
use App\Models\LocalUnit;
use App\Models\ModuleOffering;
use App\Models\PedagogicalRule;
use App\Models\Region;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

final class CreateStructureRecord
{
    public const TYPES = ['region', 'unit', 'edition', 'level', 'module', 'rule', 'offering', 'classroom'];

    // T07 will authorize the actor before calling this action. The T06 HTTP entry is local-only.
    public function handle(string $type, ?Region $region, array $input): Model
    {
        abort_unless(in_array($type, self::TYPES, true), 404);
        abort_if($type !== 'region' && $region === null, 404);

        $data = array_map(fn ($value) => is_string($value) ? trim($value) : $value, $input);
        if (isset($data['code']) && is_string($data['code'])) {
            $data['code'] = strtoupper($data['code']);
        }
        $base = ['code' => ['required', 'string', 'regex:/^[A-Z0-9][A-Z0-9_-]{1,39}$/'], 'name' => ['required', 'string', 'min:2', 'max:120']];
        $editionId = filter_var($data['curriculum_edition_id'] ?? null, FILTER_VALIDATE_INT) ?: 0;
        $offeringId = filter_var($data['module_offering_id'] ?? null, FILTER_VALIDATE_INT) ?: 0;
        $offering = $type === 'classroom' ? ModuleOffering::forRegion($region->id)->find($offeringId) : null;
        if ($type === 'classroom') {
            $editionId = $offering?->curriculum_edition_id;
        }
        $inRegion = fn (string $table) => Rule::exists($table, 'id')->where('region_id', $region?->id);
        $inEdition = fn (string $table) => $inRegion($table)->where('curriculum_edition_id', $editionId);
        $edition = ['curriculum_edition_id' => ['required', 'integer', $inRegion('curriculum_editions')]];
        $rules = match ($type) {
            'region' => $base + ['timezone' => ['required', 'timezone:all']],
            'unit' => $base,
            'edition' => $base + ['version' => ['required', 'integer', 'between:1,32767'], 'source_reference' => ['nullable', 'string', 'max:500']],
            'level' => $base + $edition + ['position' => ['required', 'integer', 'between:1,32767'], 'min_age' => ['required', 'integer', 'between:0,120'], 'max_age' => ['required', 'integer', 'between:0,120', 'gte:min_age']],
            'module' => $base + $edition + ['position' => ['required', 'integer', 'between:1,32767'], 'lesson_count' => ['required', 'integer', 'between:1,1000']],
            'rule' => $edition + ['version' => ['required', 'integer', 'between:1,32767']],
            'offering' => $base + $edition + [
                'local_unit_id' => ['required', 'integer', $inRegion('local_units')],
                'curriculum_module_id' => ['required', 'integer', $inEdition('curriculum_modules')],
                'pedagogical_rule_id' => ['required', 'integer', $inEdition('pedagogical_rules')],
                'starts_on' => ['required', 'date_format:Y-m-d'],
                'ends_on' => ['required', 'date_format:Y-m-d', 'after_or_equal:starts_on'],
                'capacity' => ['required', 'integer', 'between:1,10000'],
            ],
            'classroom' => $base + [
                'module_offering_id' => ['required', 'integer', $inRegion('module_offerings')],
                'level_id' => ['required', 'integer', $inEdition('levels')],
                'capacity' => ['required', 'integer', 'between:1,10000'],
            ],
        };

        $validated = Validator::make($data, $rules, [
            'required' => 'Preencha este campo.', 'string' => 'Informe um texto válido.',
            'integer' => 'Informe um número inteiro.', 'exists' => 'Escolha uma opção desta regional e edição.',
            'regex' => 'Use de 2 a 40 letras sem acentos, números, hífen ou sublinhado.',
            'date_format' => 'Informe uma data válida.', 'after_or_equal' => 'O fim deve ser igual ou posterior ao início.',
            'gte' => 'A idade máxima deve ser igual ou maior que a mínima.',
            'between' => 'Informe um valor entre :min e :max.', 'timezone' => 'Escolha um fuso horário válido.',
            'min' => 'Informe pelo menos :min caracteres.', 'max' => 'Use no máximo :max caracteres.',
        ])->validate();

        if ($type !== 'region') {
            $validated['region_id'] = $region->id;
        }
        if ($type === 'offering') {
            $validated['timezone'] = $region->timezone;
        }
        if ($type === 'classroom') {
            $validated['local_unit_id'] = $offering->local_unit_id;
            $validated['curriculum_edition_id'] = $offering->curriculum_edition_id;
        }
        $model = match ($type) {
            'region' => Region::class, 'unit' => LocalUnit::class, 'edition' => CurriculumEdition::class,
            'level' => Level::class, 'module' => CurriculumModule::class, 'rule' => PedagogicalRule::class,
            'offering' => ModuleOffering::class, 'classroom' => Classroom::class,
        };

        try {
            return DB::transaction(fn () => $model::create($validated)->refresh());
        } catch (QueryException $exception) {
            $message = match ($exception->errorInfo[0] ?? '') {
                '23505' => 'Já existe um registro com esse código, versão ou posição neste contexto.',
                '23P01' => 'Esta faixa de idade se sobrepõe a outro nível da mesma edição.',
                '23503' => 'As opções não pertencem ao mesmo núcleo, regional ou edição. Confira a seleção.',
                '23514' => 'Os valores não atendem às regras desta estrutura em rascunho.',
                default => null,
            };
            if ($message === null) {
                throw $exception;
            }
            throw ValidationException::withMessages([$type === 'level' && ($exception->errorInfo[0] ?? '') === '23P01' ? 'min_age' : 'structure' => $message]);
        }
    }
}
