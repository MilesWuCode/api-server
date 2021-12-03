<?php

namespace Tests\Feature\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\TestCase;

class TodoTest extends TestCase
{
    use WithFaker;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_list()
    {
        $user = \App\Models\User::inRandomOrder()->first();

        Passport::actingAs(
            $user,
            ['*']
        );

        $response = $this->get('/api/todo?page=1&limit=2&sort=idDesc');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'total',
            'per_page',
            'last_page',
            'data',
            'current_page',
        ]);
    }

    public function test_get()
    {
        $user = \App\Models\User::inRandomOrder()->first();

        Passport::actingAs(
            $user,
            ['*']
        );

        $todo = $user->todos()->inRandomOrder()->first();

        $response = $this->get('/api/todo/' . $todo->id);

        $response->assertStatus(200);
    }

    public function test_create()
    {
        $user = \App\Models\User::inRandomOrder()->first();

        Passport::actingAs(
            $user,
            ['*']
        );

        $response = $this->post('/api/todo/', [
            'content' => $this->faker->text(rand(5, 20)),
        ]);

        $response->assertStatus(200);
    }

    public function test_update()
    {
        $user = \App\Models\User::inRandomOrder()->first();

        Passport::actingAs(
            $user,
            ['*']
        );

        $todo = $user->todos()->inRandomOrder()->first();

        $response = $this->post('/api/todo/'.$todo->id, [
            'content' => $this->faker->text(rand(5, 20)),
            'active' => !$todo->active,
            '_method' => 'PUT',
        ]);

        $response->assertStatus(200);
    }

    public function test_delete()
    {
        $user = \App\Models\User::inRandomOrder()->first();

        Passport::actingAs(
            $user,
            ['*']
        );

        $todo = $user->todos()->inRandomOrder()->first();

        $response = $this->post('/api/todo/'.$todo->id, [
            '_method' => 'DELETE',
        ]);

        $response->assertStatus(200);
    }
}
