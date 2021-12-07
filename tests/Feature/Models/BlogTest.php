<?php

namespace Tests\Feature\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Laravel\Passport\Passport;

class BlogTest extends TestCase
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

        $response = $this->get('/api/blog?page=1&limit=2&sort=id_desc');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data',
            'meta' => [
                'pagination' => [
                    'total',
                    'count',
                    'per_page',
                    'current_page',
                    'total_pages',
                ]
            ],
        ]);
    }

    public function test_get()
    {
        $user = \App\Models\User::inRandomOrder()->first();

        Passport::actingAs(
            $user,
            ['*']
        );

        $blog = $user->blogs()->inRandomOrder()->first();

        $response = $this->get('/api/blog/' . $blog->id);

        $response->assertStatus(200);
    }

    public function test_create()
    {
        $user = \App\Models\User::inRandomOrder()->first();

        Passport::actingAs(
            $user,
            ['*']
        );

        $response = $this->post('/api/blog/', [
            'title' => $this->faker->text(rand(5, 200)),
            'body' => $this->faker->paragraph(),
            'status' => $this->faker->boolean(),
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

        $blog = $user->blogs()->inRandomOrder()->first();

        $response = $this->post('/api/blog/'.$blog->id, [
            'title' => $this->faker->text(rand(5, 200)),
            'body' => $this->faker->paragraph(),
            'status' => !$blog->status,
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

        $blog = $user->blogs()->inRandomOrder()->first();

        $response = $this->post('/api/blog/'.$blog->id, [
            '_method' => 'DELETE',
        ]);

        $response->assertStatus(200);
    }
}
