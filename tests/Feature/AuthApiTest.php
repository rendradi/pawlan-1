<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_via_api(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Akbar',
            'email' => 'akbar@example.com',
            'password' => 'secret123',
        ]);

        $response
            ->assertCreated()
            ->assertJson([
                'success' => true,
                'message' => 'Register berhasil',
                'data' => [
                    'name' => 'Akbar',
                    'email' => 'akbar@example.com',
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'akbar@example.com',
        ]);

        $user = User::where('email', 'akbar@example.com')->firstOrFail();

        $this->assertNotSame('secret123', $user->password);
        $this->assertTrue(Hash::check('secret123', $user->password));
    }

    public function test_user_can_login_and_receive_token(): void
    {
        User::create([
            'name' => 'Akbar',
            'email' => 'akbar@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'akbar@example.com',
            'password' => 'secret123',
        ]);

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Login berhasil',
            ])
            ->assertJsonStructure([
                'token',
            ]);
    }

    public function test_me_endpoint_requires_valid_bearer_token(): void
    {
        $response = $this->getJson('/api/me');

        $response->assertUnauthorized();
    }

    public function test_books_endpoint_requires_valid_bearer_token(): void
    {
        Book::create([
            'title' => 'Belajar Laravel',
            'author' => 'Akbar',
            'publisher' => 'Informatika',
            'year' => 2026,
        ]);

        $response = $this->getJson('/api/books');

        $response->assertUnauthorized();
    }

    public function test_books_endpoint_without_token_returns_unauthorized_for_plain_request(): void
    {
        Book::create([
            'title' => 'Belajar Laravel',
            'author' => 'Akbar',
            'publisher' => 'Informatika',
            'year' => 2026,
        ]);

        $response = $this->get('/api/books');

        $response->assertUnauthorized();
    }

    public function test_authenticated_user_can_access_profile_and_books(): void
    {
        $user = User::create([
            'name' => 'Akbar',
            'email' => 'akbar@example.com',
            'password' => Hash::make('secret123'),
        ]);

        Book::create([
            'title' => 'Belajar Laravel',
            'author' => 'Akbar',
            'publisher' => 'Informatika',
            'year' => 2026,
        ]);

        $loginResponse = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'secret123',
        ]);

        $token = $loginResponse->json('token');

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/me')
            ->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Data user',
                'data' => [
                    'email' => $user->email,
                ],
            ]);

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/books')
            ->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'List Data Books',
            ]);
    }
}
