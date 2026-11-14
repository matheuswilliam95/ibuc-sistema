<?php

namespace Database\Seeders;

use App\Models\Classroom;
use App\Models\CurriculumEdition;
use App\Models\CurriculumModule;
use App\Models\Level;
use App\Models\LocalUnit;
use App\Models\ModuleOffering;
use App\Models\PedagogicalRule;
use App\Models\Region;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class StructureDemoSeeder extends Seeder
{
    public function run(): void
    {
        $connection = config('database.default');
        if (! app()->environment(['local', 'testing']) || DB::getDriverName() !== 'pgsql'
            || ! in_array(config("database.connections.$connection.host"), ['127.0.0.1', '::1', 'localhost'], true)
            || ! in_array(config("database.connections.$connection.url"), [null, ''], true)
            || ! in_array(DB::selectOne('select current_database() as name')->name, ['ibuc_structure_demo', 'ibuc_structure_test'], true)) {
            throw new RuntimeException('Dados fictícios somente nas bases PostgreSQL locais dedicadas à estrutura.');
        }

        DB::transaction(function () {
            foreach (['AURORA' => 'Regional Aurora', 'HORIZONTE' => 'Regional Horizonte'] as $code => $name) {
                $region = Region::firstOrCreate(['code' => 'DEMO-'.$code], ['name' => $name, 'timezone' => 'America/Sao_Paulo']);
                $edition = CurriculumEdition::firstOrCreate(['region_id' => $region->id, 'code' => 'PILOTO', 'version' => 1], ['name' => 'Currículo do piloto — proposta', 'source_reference' => 'Faixas escolhidas pelo usuário; edição completa e vigência pendentes em D02.']);
                $levels = collect([[2, 5], [6, 8], [9, 11], [12, 17]])->map(fn ($ages, $i) => Level::firstOrCreate([
                    'region_id' => $region->id, 'curriculum_edition_id' => $edition->id, 'code' => 'N'.($i + 1),
                ], ['name' => 'Nível '.($i + 1), 'position' => $i + 1, 'min_age' => $ages[0], 'max_age' => $ages[1]]));
                $module = CurriculumModule::firstOrCreate(['region_id' => $region->id, 'curriculum_edition_id' => $edition->id, 'code' => 'MOD-01'], ['name' => 'Módulo de demonstração', 'position' => 1, 'lesson_count' => 12]);
                $rule = PedagogicalRule::firstOrCreate(['region_id' => $region->id, 'curriculum_edition_id' => $edition->id, 'version' => 1]);
                $unitNames = $code === 'AURORA' ? ['JARDIM' => 'Núcleo Jardim', 'PONTE' => 'Núcleo Ponte'] : ['FAROL' => 'Núcleo Farol'];
                foreach ($unitNames as $unitCode => $unitName) {
                    $unit = LocalUnit::firstOrCreate(['region_id' => $region->id, 'code' => $unitCode], ['name' => $unitName]);
                    $offering = ModuleOffering::firstOrCreate(['local_unit_id' => $unit->id, 'code' => 'OFERTA-DEMO'], [
                        'region_id' => $region->id, 'curriculum_edition_id' => $edition->id, 'curriculum_module_id' => $module->id,
                        'pedagogical_rule_id' => $rule->id, 'name' => 'Oferta de demonstração', 'starts_on' => '2027-02-02',
                        'ends_on' => '2027-04-27', 'timezone' => $region->timezone, 'capacity' => 50,
                    ]);
                    $classNames = $unitCode === 'PONTE' ? [2 => 'Caminhos', 3 => 'Novos Horizontes'] : [0 => 'Sementes', 1 => 'Descobertas'];
                    foreach ($classNames as $i => $className) {
                        Classroom::firstOrCreate(['module_offering_id' => $offering->id, 'code' => 'TURMA-N'.($i + 1)], [
                            'region_id' => $region->id, 'local_unit_id' => $unit->id, 'curriculum_edition_id' => $edition->id,
                            'level_id' => $levels[$i]->id, 'name' => $className, 'capacity' => 25,
                        ]);
                    }
                }
            }
        });
    }
}
