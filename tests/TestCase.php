<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->disableForeignKeyConstraints();
        $this->artisan('migrate:fresh', ['--seed' => true]);
        $this->enableForeignKeyConstraints();
    }

    protected function disableForeignKeyConstraints()
    {
        $dbDriver = DB::getDriverName();

        if ($dbDriver === 'mysql') {
            Schema::disableForeignKeyConstraints();
        } elseif ($dbDriver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=off');
        }
    }

    protected function enableForeignKeyConstraints()
    {
        $dbDriver = DB::getDriverName();

        if ($dbDriver === 'mysql') {
            Schema::enableForeignKeyConstraints();
        } elseif ($dbDriver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=on');
        }
    }

    protected function refreshTestDatabase()
    {
        if (!RefreshDatabaseState::$migrated) {
            $this->disableForeignKeyConstraints();

            $this->artisan('migrate:fresh', $this->migrateFreshUsing());

            $this->enableForeignKeyConstraints();

            $this->app[Kernel::class]->setArtisan(null);

            RefreshDatabaseState::$migrated = true;
        }

        $this->beginDatabaseTransaction();
    }
}
