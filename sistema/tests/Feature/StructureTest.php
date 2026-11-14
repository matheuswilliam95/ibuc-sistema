<?php

namespace Tests\Feature;

use App\Actions\Structure\CreateStructureRecord;
use App\Models\Classroom;
use App\Models\CurriculumEdition;
use App\Models\Level;
use App\Models\ModuleOffering;
use App\Models\PedagogicalRule;
use App\Models\Region;
use App\Queries\ClassroomDirectory;
use Database\Seeders\StructureDemoSeeder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\PostgresTestCase;

class StructureTest extends PostgresTestCase
{
    private function scenario(): array
    {
        config(['structure.demo_enabled' => true]);
        $this->seed(StructureDemoSeeder::class);

        return [Region::where('code', 'DEMO-AURORA')->firstOrFail(), Region::where('code', 'DEMO-HORIZONTE')->firstOrFail()];
    }

    public function test_demo_seed_is_repeatable_and_creates_no_user_or_approved_rule(): void
    {
        $this->scenario();
        $this->seed(StructureDemoSeeder::class);
        foreach (['regions' => 2, 'local_units' => 3, 'curriculum_editions' => 2, 'levels' => 8, 'curriculum_modules' => 2, 'pedagogical_rules' => 2, 'module_offerings' => 3, 'classrooms' => 6, 'users' => 0] as $table => $count) {
            $this->assertDatabaseCount($table, $count);
        }
        $this->assertNull(PedagogicalRule::first()->cutoff_policy);
        $this->assertNull(PedagogicalRule::first()->valid_from);
        $this->assertSame('draft', CurriculumEdition::first()->status);
        $this->assertSame([[2, 5], [6, 8], [9, 11], [12, 17]], Level::forRegion(Region::first()->id)->orderBy('position')->get()->map(fn ($level) => [$level->min_age, $level->max_age])->all());
    }

    public function test_all_records_can_be_created_and_relationships_are_derived_from_the_offering(): void
    {
        $create = app(CreateStructureRecord::class);
        $region = $create->handle('region', null, ['code' => 'nova', 'name' => 'Regional Exemplo', 'timezone' => 'America/Manaus']);
        $unit = $create->handle('unit', $region, ['code' => 'SEDE', 'name' => 'Núcleo Exemplo']);
        $edition = $create->handle('edition', $region, ['code' => 'CURSO', 'name' => 'Edição Exemplo', 'version' => 2]);
        $level = $create->handle('level', $region, ['code' => 'N1', 'name' => 'Nível Exemplo', 'curriculum_edition_id' => $edition->id, 'position' => 1, 'min_age' => 2, 'max_age' => 5]);
        $module = $create->handle('module', $region, ['code' => 'M1', 'name' => 'Módulo Exemplo', 'curriculum_edition_id' => $edition->id, 'position' => 1, 'lesson_count' => 12]);
        $rule = $create->handle('rule', $region, ['curriculum_edition_id' => $edition->id, 'version' => 1, 'cutoff_policy' => 'birthday', 'status' => 'active']);
        $offering = $create->handle('offering', $region, ['code' => 'OF-01', 'name' => 'Oferta Exemplo', 'local_unit_id' => $unit->id, 'curriculum_edition_id' => $edition->id, 'curriculum_module_id' => $module->id, 'pedagogical_rule_id' => $rule->id, 'starts_on' => '2027-02-02', 'ends_on' => '2027-04-27', 'capacity' => 50]);
        $classroom = $create->handle('classroom', $region, ['code' => 'S1', 'name' => 'Sementes', 'module_offering_id' => $offering->id, 'level_id' => $level->id, 'capacity' => 25, 'local_unit_id' => 999, 'curriculum_edition_id' => 999, 'status' => 'active']);
        $this->assertSame('NOVA', $region->code);
        $this->assertTrue($classroom->localUnit->is($unit));
        $this->assertTrue($classroom->offering->module->is($module));
        $this->assertTrue($classroom->edition->is($edition));
        $this->assertSame('America/Manaus', $offering->timezone);
        $this->assertSame('draft', $classroom->status);
        $this->assertSame('draft', $rule->status);
        $this->assertNull($rule->cutoff_policy);
        $this->assertNull($offering->actual_started_at);
    }

    public function test_homonymous_classrooms_are_separated_by_region_and_unit(): void
    {
        [$aurora, $horizonte] = $this->scenario();
        $directory = app(ClassroomDirectory::class);
        $first = $directory->search($aurora, null, 'sementes');
        $second = $directory->search($horizonte, null, 'Sementes');
        $this->assertCount(1, $first);
        $this->assertCount(1, $second);
        $this->assertNotSame($first[0]->id, $second[0]->id);
        $this->assertSame('Núcleo Jardim', $first[0]->localUnit->name);
        $this->assertSame('Núcleo Farol', $second[0]->localUnit->name);
        $this->assertCount(0, $directory->search($aurora, $aurora->localUnits()->where('code', 'PONTE')->first()->id, 'Sementes'));
        $this->assertCount(0, $directory->search($aurora, null, '%'));
        $this->assertCount(0, $directory->search($aurora, null, '_'));
        $this->expectException(ModelNotFoundException::class);
        $directory->search($aurora, $horizonte->localUnits()->first()->id);
    }

    public static function invalidClassroomScopes(): array
    {
        return [['region_id'], ['local_unit_id'], ['curriculum_edition_id'], ['level_id'], ['module_offering_id']];
    }

    #[DataProvider('invalidClassroomScopes')]
    public function test_database_rejects_forged_classroom_scope_even_without_the_action(string $field): void
    {
        [$aurora, $horizonte] = $this->scenario();
        $source = Classroom::forRegion($aurora->id)->first()->getAttributes();
        $foreign = Classroom::forRegion($horizonte->id)->first();
        unset($source['id']);
        $source['code'] = 'FORGED';
        $source[$field] = $foreign->$field;
        $this->expectException(QueryException::class);
        $this->expectExceptionCode('23503');
        DB::table('classrooms')->insert($source);
    }

    public static function invalidOfferingScopes(): array
    {
        return [['local_unit_id'], ['curriculum_module_id'], ['pedagogical_rule_id']];
    }

    #[DataProvider('invalidOfferingScopes')]
    public function test_database_rejects_offering_with_foreign_unit_module_or_rule(string $field): void
    {
        [$aurora, $horizonte] = $this->scenario();
        $source = ModuleOffering::forRegion($aurora->id)->first()->getAttributes();
        $foreign = ModuleOffering::forRegion($horizonte->id)->first();
        unset($source['id']);
        $source['code'] = 'FORGED';
        $source[$field] = $foreign->$field;
        $this->expectException(QueryException::class);
        $this->expectExceptionCode('23503');
        DB::table('module_offerings')->insert($source);
    }

    public function test_database_prevents_moving_a_unit_with_existing_offerings_to_another_region(): void
    {
        [$aurora, $horizonte] = $this->scenario();
        $unit = $aurora->localUnits()->first();
        $this->expectException(QueryException::class);
        $this->expectExceptionCode('23503');
        DB::table('local_units')->where('id', $unit->id)->update(['region_id' => $horizonte->id]);
    }

    public function test_referenced_edition_cannot_be_deleted(): void
    {
        [$region] = $this->scenario();
        $this->expectException(QueryException::class);
        $this->expectExceptionCode('23503');
        $region->editions()->first()->delete();
    }

    public static function incompatibleEditionReferences(): array
    {
        return [['classrooms', 'level_id', 'level'], ['module_offerings', 'curriculum_module_id', 'module'], ['module_offerings', 'pedagogical_rule_id', 'rule']];
    }

    #[DataProvider('incompatibleEditionReferences')]
    public function test_same_region_still_cannot_mix_editions(string $table, string $column, string $type): void
    {
        [$region] = $this->scenario();
        $create = app(CreateStructureRecord::class);
        $edition = $create->handle('edition', $region, ['code' => 'OUTRA', 'name' => 'Outra edição', 'version' => 1]);
        $related = $create->handle($type, $region, ['curriculum_edition_id' => $edition->id, 'code' => 'ITEM-01', 'name' => 'Outro item', 'version' => 1, 'position' => 1, 'min_age' => 2, 'max_age' => 5, 'lesson_count' => 12]);
        $this->expectException(QueryException::class);
        $this->expectExceptionCode('23503');
        DB::table($table)->where('id', DB::table($table)->value('id'))->update([$column => $related->id]);
    }

    public function test_structure_migration_can_be_reverted_and_reapplied_in_isolated_transaction(): void
    {
        $this->scenario();
        $migration = require database_path('migrations/2026_11_14_000001_create_organization_and_curriculum_tables.php');
        $migration->down();
        $this->assertFalse(Schema::hasTable('classrooms'));
        $this->assertTrue(Schema::hasTable('users'));
        $migration->up();
        foreach (['regions', 'local_units', 'curriculum_editions', 'levels', 'curriculum_modules', 'pedagogical_rules', 'module_offerings', 'classrooms'] as $table) {
            $this->assertDatabaseCount($table, 0);
        }
    }

    public function test_overlapping_age_ranges_are_rejected_and_reported_without_sql_details(): void
    {
        [$region] = $this->scenario();
        try {
            app(CreateStructureRecord::class)->handle('level', $region, ['code' => 'OVERLAP', 'name' => 'Faixa conflitante', 'curriculum_edition_id' => $region->editions()->first()->id, 'position' => 5, 'min_age' => 5, 'max_age' => 6]);
            $this->fail('Uma faixa sobreposta foi aceita.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('min_age', $exception->errors());
            $this->assertStringNotContainsString('SQLSTATE', $exception->getMessage());
        }
        $this->assertSame(4, Level::forRegion($region->id)->count());
    }

    public static function invalidDatabaseValues(): array
    {
        return [
            ['levels', 'min_age', -1], ['levels', 'max_age', 1], ['levels', 'position', 0],
            ['curriculum_modules', 'lesson_count', 0], ['curriculum_editions', 'version', 0],
            ['module_offerings', 'capacity', 0], ['module_offerings', 'ends_on', '2020-01-01'],
            ['module_offerings', 'actual_started_at', '2027-02-02 12:00:00+00'],
            ['classrooms', 'capacity', 0], ['classrooms', 'status', 'active'],
            ['curriculum_editions', 'status', 'active'], ['pedagogical_rules', 'status', 'approved'],
        ];
    }

    #[DataProvider('invalidDatabaseValues')]
    public function test_database_rejects_invalid_values_and_unapproved_activation(string $table, string $field, mixed $value): void
    {
        $this->scenario();
        $this->expectException(QueryException::class);
        $this->expectExceptionCode('23514');
        DB::table($table)->where('id', DB::table($table)->value('id'))->update([$field => $value]);
    }

    public function test_http_page_and_options_use_only_the_selected_region(): void
    {
        [$aurora, $horizonte] = $this->scenario();
        $this->get('/estrutura?region='.$aurora->id.'&q=Sementes')->assertOk()->assertSee('Núcleo Jardim')->assertDontSee('Núcleo Farol')->assertHeader('Cache-Control', 'no-store, private');
        $this->get('/estrutura?region='.$horizonte->id.'&q=Sementes')->assertOk()->assertSee('Núcleo Farol')->assertDontSee('Núcleo Jardim');
        $this->get('/estrutura?region='.$aurora->id.'&unit='.$horizonte->localUnits()->first()->id)->assertNotFound();
    }

    public function test_http_store_persists_a_classroom_and_rejects_a_foreign_level(): void
    {
        [$aurora, $horizonte] = $this->scenario();
        $offering = ModuleOffering::forRegion($aurora->id)->first();
        $input = ['region_id' => $aurora->id, 'name' => 'Turma de teste', 'code' => 'HTTP-01', 'module_offering_id' => $offering->id, 'level_id' => Level::forRegion($horizonte->id)->first()->id, 'capacity' => 20];
        $this->from('/estrutura?region='.$aurora->id)->post('/estrutura/classroom', $input)->assertRedirect()->assertSessionHasErrors('level_id');
        $this->assertDatabaseMissing('classrooms', ['code' => 'HTTP-01']);
        $input['level_id'] = Level::forRegion($aurora->id)->first()->id;
        $this->post('/estrutura/classroom', $input)->assertRedirect();
        $this->assertDatabaseHas('classrooms', ['code' => 'HTTP-01', 'region_id' => $aurora->id, 'local_unit_id' => $offering->local_unit_id, 'status' => 'draft']);
        $this->post('/estrutura/classroom', $input)->assertSessionHasErrors('structure');
        $this->assertSame(1, Classroom::where('code', 'HTTP-01')->count());
    }

    public function test_post_rejects_malformed_ids_instead_of_using_another_scope(): void
    {
        [$region] = $this->scenario();
        $this->postJson('/estrutura/classroom', ['region_id' => $region->id, 'module_offering_id' => ['bad'], 'level_id' => ['bad']])->assertUnprocessable()->assertJsonValidationErrors(['module_offering_id', 'level_id']);
    }

    public function test_demo_entry_denies_remote_requests_disabled_mode_and_production(): void
    {
        $this->scenario();
        $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.5'])->get('/estrutura')->assertNotFound();
        $this->withServerVariables(['REMOTE_ADDR' => '127.0.0.1']);
        config(['structure.demo_enabled' => false]);
        $this->get('/estrutura')->assertNotFound();
        $this->post('/estrutura/region', ['code' => 'REAL', 'name' => 'Inacessível', 'timezone' => 'America/Sao_Paulo'])->assertNotFound();
        config(['structure.demo_enabled' => true]);
        $this->app['env'] = 'production';
        $this->get('/estrutura')->assertNotFound();
    }

    public function test_demo_seeder_refuses_production(): void
    {
        $this->app['env'] = 'production';
        $this->expectException(\RuntimeException::class);
        (new StructureDemoSeeder)->run();
    }
}
