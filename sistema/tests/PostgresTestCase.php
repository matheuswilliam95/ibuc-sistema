<?php

namespace Tests;

use Illuminate\Support\Facades\DB;

abstract class PostgresTestCase extends TestCase
{
    private bool $transactionStarted = false;

    protected function setUp(): void
    {
        parent::setUp();
        if (getenv('IBUC_POSTGRES_TESTS') !== '1') {
            $this->markTestSkipped('Execute scripts/local-postgres.ps1 -Action Test para verificar a integridade no PostgreSQL isolado.');
        }
        $connection = config('database.default');
        $this->assertSame('pgsql', config("database.connections.$connection.driver"));
        $this->assertContains(config("database.connections.$connection.host"), ['127.0.0.1', '::1', 'localhost']);
        $this->assertContains(config("database.connections.$connection.url"), [null, '']);
        $this->assertSame('ibuc_structure_test', config("database.connections.$connection.database"));
        $this->assertSame('ibuc_structure_test', DB::selectOne('select current_database() as name')->name);
        $this->artisan('migrate', ['--force' => true])->assertExitCode(0);
        DB::beginTransaction();
        $this->transactionStarted = true;
    }

    protected function tearDown(): void
    {
        if ($this->transactionStarted) {
            while (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
        }
        parent::tearDown();
    }
}
