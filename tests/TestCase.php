<?php

namespace Yajra\Oci8\Tests;

use Illuminate\Database\Schema\Blueprint;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Yajra\Oci8\Oci8ServiceProvider;
use Yajra\Oci8\Oci8ValidationServiceProvider;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->migrateDatabase();

        $this->seedDatabase();
    }

    protected function migrateDatabase(): void
    {
        /** @var \Illuminate\Database\Schema\Builder $schemaBuilder */
        $schemaBuilder = $this->app['db']->connection()->getSchemaBuilder();
        if ($schemaBuilder->hasTable('users')) {
            $schemaBuilder->drop('users');
        }

        $schemaBuilder->create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email');
            $table->timestamps();
        });

        if ($schemaBuilder->hasTable('multi_blobs')) {
            $schemaBuilder->drop('multi_blobs');
        }

        $schemaBuilder->create('multi_blobs', function (Blueprint $table) {
            $table->increments('id');
            $table->binary('blob_1')->nullable();
            $table->binary('blob_2')->nullable();
            $table->integer('status')->nullable();
            $table->timestamps();
        });

        if ($schemaBuilder->hasTable('jobs')) {
            $schemaBuilder->drop('jobs');
        }

        $schemaBuilder->create('jobs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->integer('job_id')->nullable();
        });
    }

    protected function seedDatabase(): void
    {
        collect(range(1, 20))->each(function ($i) {
            /** @var User $user */
            User::query()->create([
                'name' => 'Record-'.$i,
                'email' => 'Email-'.$i.'@example.com',
            ]);
        });
    }

    /**
     * Set up the environment.
     *
     * @param  \Illuminate\Foundation\Application  $app
     */
    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('app.debug', true);
        $app['config']->set('database.default', 'oracle');
        $app['config']->set('database.connections.oracle', [
            'driver' => 'oracle',
            'host' => 'localhost',
            'database' => 'free',
            'service_name' => 'free',
            'username' => 'oci8',
            'password' => 'secret',
            'port' => 1521,
        ]);
    }

    protected function getPackageProviders($app): array
    {
        return [
            Oci8ServiceProvider::class,
            Oci8ValidationServiceProvider::class,
        ];
    }
}
