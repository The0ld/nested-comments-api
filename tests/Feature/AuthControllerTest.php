<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a user for testing
        $this->user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);
    }

    #[test]
    public function it_logs_in_successfully_with_valid_credentials()
    {
        $response = $this->postJson('/api/v1/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) =>
                $json->where('status', 'success')
                     ->where('message', 'Login successful')
                     ->has('data.token')
                     ->etc()
            );
    }

    #[test]
    public function it_fails_login_with_invalid_credentials()
    {
        $response = $this->postJson('/api/v1/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
            ->assertJson(fn (AssertableJson $json) =>
                $json->where('status', 'error')
                     ->where('message', 'Invalid login credentials')
                     ->missing('data')
            );
    }

    #[test]
    public function it_fails_login_with_validation_errors()
    {
        // Case 1: Missing email
        $response = $this->postJson('/api/v1/login', [
            'password' => 'password',
        ]);

        $response->assertStatus(422)
            ->assertJson(fn (AssertableJson $json) =>
            $json->where('status', 'error')
                ->has('message')
                ->has('errors.email')
                ->missing('data')
            );

        // Case 2: Missing password
        $response = $this->postJson('/api/v1/login', [
            'email' => 'test@example.com',
        ]);

        $response->assertStatus(422)
            ->assertJson(fn (AssertableJson $json) =>
            $json->where('status', 'error')
                ->has('message')
                ->has('errors.password')
                ->missing('data')
            );
    }
}
