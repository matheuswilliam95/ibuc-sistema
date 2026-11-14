<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            throw new RuntimeException('A estrutura IBUC exige PostgreSQL para suas restrições de integridade.');
        }

        Schema::create('regions', function (Blueprint $table) {
            $table->id();
            $table->string('code', 40)->unique();
            $table->string('name', 120);
            $table->string('timezone', 60)->default('America/Sao_Paulo');
            $table->timestampsTz();
        });

        Schema::create('local_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('region_id')->constrained()->restrictOnDelete();
            $table->string('code', 40);
            $table->string('name', 120);
            $table->timestampsTz();
            $table->unique(['region_id', 'code']);
            $table->unique(['id', 'region_id']);
        });

        Schema::create('curriculum_editions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('region_id')->constrained()->restrictOnDelete();
            $table->string('code', 40);
            $table->unsignedSmallInteger('version');
            $table->string('name', 120);
            $table->string('status', 20)->default('draft');
            $table->string('source_reference', 500)->nullable();
            $table->timestampsTz();
            $table->unique(['region_id', 'code', 'version'], 'editions_region_code_version_unique');
            $table->unique(['id', 'region_id']);
        });

        Schema::create('levels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('region_id');
            $table->foreignId('curriculum_edition_id');
            $table->string('code', 40);
            $table->string('name', 120);
            $table->unsignedSmallInteger('position');
            $table->unsignedSmallInteger('min_age');
            $table->unsignedSmallInteger('max_age');
            $table->timestampsTz();
            $table->foreign(['curriculum_edition_id', 'region_id'])->references(['id', 'region_id'])->on('curriculum_editions')->restrictOnDelete();
            $table->unique(['curriculum_edition_id', 'code']);
            $table->unique(['curriculum_edition_id', 'position']);
            $table->unique(['id', 'curriculum_edition_id', 'region_id'], 'levels_scope_unique');
        });

        Schema::create('curriculum_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('region_id');
            $table->foreignId('curriculum_edition_id');
            $table->string('code', 40);
            $table->string('name', 120);
            $table->unsignedSmallInteger('position');
            $table->unsignedSmallInteger('lesson_count');
            $table->timestampsTz();
            $table->foreign(['curriculum_edition_id', 'region_id'])->references(['id', 'region_id'])->on('curriculum_editions')->restrictOnDelete();
            $table->unique(['curriculum_edition_id', 'code']);
            $table->unique(['curriculum_edition_id', 'position']);
            $table->unique(['id', 'curriculum_edition_id', 'region_id'], 'modules_scope_unique');
        });

        Schema::create('pedagogical_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('region_id');
            $table->foreignId('curriculum_edition_id');
            $table->unsignedSmallInteger('version');
            $table->string('status', 20)->default('draft');
            $table->string('cutoff_policy', 30)->nullable();
            $table->string('transition_policy', 30)->nullable();
            $table->string('leap_day_policy', 20)->nullable();
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();
            $table->timestampsTz();
            $table->foreign(['curriculum_edition_id', 'region_id'])->references(['id', 'region_id'])->on('curriculum_editions')->restrictOnDelete();
            $table->unique(['curriculum_edition_id', 'version']);
            $table->unique(['id', 'curriculum_edition_id', 'region_id'], 'rules_scope_unique');
        });

        Schema::create('module_offerings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('region_id');
            $table->foreignId('local_unit_id');
            $table->foreignId('curriculum_edition_id');
            $table->foreignId('curriculum_module_id');
            $table->foreignId('pedagogical_rule_id');
            $table->string('code', 40);
            $table->string('name', 120);
            $table->string('status', 20)->default('draft');
            $table->date('starts_on');
            $table->date('ends_on');
            $table->timestampTz('actual_started_at')->nullable();
            $table->string('timezone', 60);
            $table->unsignedSmallInteger('capacity');
            $table->timestampsTz();
            $table->foreign(['local_unit_id', 'region_id'])->references(['id', 'region_id'])->on('local_units')->restrictOnDelete();
            $table->foreign(['curriculum_module_id', 'curriculum_edition_id', 'region_id'], 'offerings_module_scope_fk')->references(['id', 'curriculum_edition_id', 'region_id'])->on('curriculum_modules')->restrictOnDelete();
            $table->foreign(['pedagogical_rule_id', 'curriculum_edition_id', 'region_id'], 'offerings_rule_scope_fk')->references(['id', 'curriculum_edition_id', 'region_id'])->on('pedagogical_rules')->restrictOnDelete();
            $table->unique(['local_unit_id', 'code']);
            $table->unique(['id', 'curriculum_edition_id', 'region_id', 'local_unit_id'], 'offerings_scope_unique');
            $table->index(['region_id', 'local_unit_id', 'starts_on']);
        });

        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('region_id');
            $table->foreignId('local_unit_id');
            $table->foreignId('curriculum_edition_id');
            $table->foreignId('module_offering_id');
            $table->foreignId('level_id');
            $table->string('code', 40);
            $table->string('name', 120);
            $table->unsignedSmallInteger('capacity');
            $table->string('status', 20)->default('draft');
            $table->timestampsTz();
            $table->foreign(['module_offering_id', 'curriculum_edition_id', 'region_id', 'local_unit_id'], 'classrooms_offering_scope_fk')->references(['id', 'curriculum_edition_id', 'region_id', 'local_unit_id'])->on('module_offerings')->restrictOnDelete();
            $table->foreign(['level_id', 'curriculum_edition_id', 'region_id'], 'classrooms_level_scope_fk')->references(['id', 'curriculum_edition_id', 'region_id'])->on('levels')->restrictOnDelete();
            $table->unique(['module_offering_id', 'code']);
            $table->index(['region_id', 'local_unit_id', 'name']);
        });

        foreach (['curriculum_editions', 'pedagogical_rules', 'module_offerings', 'classrooms'] as $table) {
            DB::statement("ALTER TABLE {$table} ADD CONSTRAINT {$table}_draft_only CHECK (status = 'draft')");
        }
        foreach (['regions', 'local_units', 'curriculum_editions', 'levels', 'curriculum_modules', 'module_offerings', 'classrooms'] as $table) {
            DB::statement("ALTER TABLE {$table} ADD CONSTRAINT {$table}_valid_identity CHECK (code ~ '^[A-Z0-9][A-Z0-9_-]{1,39}$' AND length(btrim(name)) BETWEEN 2 AND 120)");
        }
        DB::statement('ALTER TABLE curriculum_editions ADD CONSTRAINT editions_positive_version CHECK (version > 0)');
        DB::statement('ALTER TABLE levels ADD CONSTRAINT levels_valid_range CHECK (min_age >= 0 AND max_age >= min_age AND max_age <= 120 AND position > 0)');
        // Both range operators are native: no btree_gist extension or administrator permission needed.
        DB::statement("ALTER TABLE levels ADD CONSTRAINT levels_no_age_overlap EXCLUDE USING gist (int8range(curriculum_edition_id, curriculum_edition_id, '[]') WITH &&, int4range(min_age::int, max_age::int, '[]') WITH &&)");
        DB::statement('ALTER TABLE curriculum_modules ADD CONSTRAINT modules_positive_counts CHECK (position > 0 AND lesson_count BETWEEN 1 AND 1000)');
        DB::statement("ALTER TABLE pedagogical_rules ADD CONSTRAINT rules_valid_draft CHECK (version > 0 AND (valid_until IS NULL OR valid_from IS NOT NULL AND valid_until >= valid_from) AND (cutoff_policy IS NULL OR cutoff_policy IN ('offering_start', 'annual', 'birthday')) AND (transition_policy IS NULL OR transition_policy IN ('next_offering', 'manual_mid_module')) AND (leap_day_policy IS NULL OR leap_day_policy IN ('february_28', 'march_1')))");
        DB::statement('ALTER TABLE module_offerings ADD CONSTRAINT offerings_valid_draft CHECK (ends_on >= starts_on AND capacity BETWEEN 1 AND 10000 AND actual_started_at IS NULL)');
        DB::statement('ALTER TABLE classrooms ADD CONSTRAINT classrooms_positive_capacity CHECK (capacity BETWEEN 1 AND 10000)');
    }

    public function down(): void
    {
        foreach (['classrooms', 'module_offerings', 'pedagogical_rules', 'curriculum_modules', 'levels', 'curriculum_editions', 'local_units', 'regions'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
