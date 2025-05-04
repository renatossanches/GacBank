<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;
    public function test_user_registration()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'cpf' => '123.456.789-01',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);
    
        $response->assertStatus(201)
            ->assertJson(['message' => 'Usuário registrado com sucesso']);
    }
    
    public function test_user_login()
    {
        // Crie um usuário diretamente para evitar problemas com factories
        $user = User::create([
            'name' => 'Test User',
            'email' => 'testlogin@example.com',
            'cpf' => '12345678901',
            'password' => Hash::make('password123')
        ]);
    
        $response = $this->postJson('/api/auth', [
            'email' => 'testlogin@example.com',
            'password' => 'password123'
        ]);
    
        $response->assertStatus(200)
            ->assertJsonStructure([
                'token',
                'user' => [
                    'id',
                    'name',
                    'email',
                    'cpf'
                ]
            ]);
    }
}