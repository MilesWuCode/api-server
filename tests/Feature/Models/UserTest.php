<?php

namespace Tests\Feature\Models;

use App\Models\User;
use Illuminate\Support\Str;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;

final class UserTest extends TestCase
{
    use WithFaker;

    public function test_get()
    {
        Passport::actingAs(
            User::factory()->create(),
            ['*']
        );

        $response = $this->getJson('/api/user');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email',
                'email_verified_at',
                'created_at',
                'updated_at',
            ],
        ]);
    }

    public function test_update()
    {
        $user = \App\Models\User::inRandomOrder()->first();

        Passport::actingAs(
            $user,
            ['*']
        );

        $response = $this->post('/api/user', [
            'name' => $this->faker->name(),
            '_method' => 'PUT',
        ]);

        $response->assertStatus(200);
    }

    public function test_change_password()
    {
        $user = \App\Models\User::inRandomOrder()->first();

        Passport::actingAs(
            $user,
            ['*']
        );

        $password = Str::random(8);

        $response = $this->post('/api/user/change-password', [
            'password' => $password,
            'comfirm_password' => $password,
            '_method' => 'PUT',
        ]);

        $response->assertStatus(200);
    }
}
