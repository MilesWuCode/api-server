<?php

namespace Tests\Feature\Models;

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Passport;
use Tests\TestCase;

final class AuthTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_register()
    {
        $user = User::factory()->make();

        $response = $this->postJson('/api/auth/register', [
            'name' => $user->name,
            'email' => $user->email,
            'password' => 'password',
            'comfirm_password' => 'password',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('users', [
            'name' => $user->name,
            'email' => $user->email,
        ]);

        $this->assertTrue(
            Hash::check('password', User::where('email', $user->email)->first()->password)
        );
    }

    public function test_send_verify_email()
    {
        $user = User::factory()->make();

        $user->email_verified_at = null;

        $user->save();

        $response = $this->postJson('/api/auth/send-verify-email', [
            'email' => $user->email,
        ]);

        $response->assertStatus(200);
    }

    public function test_verify_email()
    {
        $user = User::factory()->make();

        $user->email_verified_at = null;

        $user->save();

        $this->postJson('/api/auth/send-verify-email', [
            'email' => $user->email,
        ]);

        $user->verifies->last()->code;

        $response = $this->postJson('/api/auth/verify-email', [
            'id' => $user->id,
            'hash' => sha1($user->email),
            'expires' => Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60))->getTimestamp(),
            'code' => $user->verifies->last()->code
        ]);

        $response->assertStatus(200);
    }

    public function test_login()
    {
        $user = User::factory()->create();

        $client = new ClientRepository();

        $passwordGrant = $client->find(2);

        $response = $this->postJson('/oauth/token', [
            'grant_type' => 'password',
            'client_id' => $passwordGrant->id,
            'client_secret' => $passwordGrant->secret,
            'username' => $user->email,
            'password' => 'password',
            'scope' => '*',
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'token_type',
            'expires_in',
            'access_token',
            'refresh_token',
        ]);
    }

    public function test_logout()
    {
        Passport::actingAs(
            User::factory()->create(),
            ['*']
        );

        $response = $this->postJson('/api/auth/logout');

        $response->assertStatus(200);
    }
}
