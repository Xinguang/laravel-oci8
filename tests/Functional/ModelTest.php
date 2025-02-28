<?php

namespace Yajra\Oci8\Tests\Functional;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\Test;
use Yajra\Oci8\Tests\MultiBlob;
use Yajra\Oci8\Tests\TestCase;
use Yajra\Oci8\Tests\User;
use Yajra\Oci8\Tests\UserWithGuardedProperty;

class ModelTest extends TestCase
{
    use DatabaseTransactions;

    #[Test]
    public function it_can_fill_a_model_to_create_a_record()
    {
        $attributes = [
            'name' => 'John',
            'email' => 'john@example.com',
        ];

        $model = new User;
        $model->fill($attributes);
        $model->save();

        $this->assertDatabaseHas('users', $attributes);
    }

    #[Test]
    public function it_can_insert_record_using_a_model()
    {
        User::query()->insert($attributes = [
            'name' => 'John',
            'email' => 'john@example.com',
        ]);

        $this->assertDatabaseHas('users', $attributes);
    }

    #[Test]
    public function it_can_create_guarded_model_by_setting_the_property()
    {
        $count = UserWithGuardedProperty::count();

        $user = new UserWithGuardedProperty;
        $user->name = 'Test';
        $user->email = 'test@example.com';
        $user->save();

        $this->assertDatabaseCount('users', $count + 1);
    }

    #[Test]
    public function it_can_create_guarded_model_using_create_method()
    {
        $count = UserWithGuardedProperty::count();

        UserWithGuardedProperty::create([
            'name' => 'Test',
            'email' => 'test@example.com',
        ]);

        $this->assertDatabaseCount('users', $count + 1);
    }

    #[Test]
    public function it_can_update_model_with_mutliple_blob_columns()
    {
        $multiBlob = MultiBlob::create();
        $multiBlob->blob_1 = ['test'];
        $multiBlob->blob_2 = ['test2'];
        $multiBlob->status = 1;
        $multiBlob->save();

        $this->assertDatabaseHas('multi_blobs', ['status' => 1]);
    }
}
