<?php

namespace Tests\Feature;

use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Flat;
use App\Models\Email;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered()
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register()
    {
        Email::create(['email' => 'test@example.com']);
        Flat::create([
            'number' => '29',
            'square' => 100,
            'warmCounter' => 1,
            'useLift' => 1,
            'privilege' => 0,
            'name' => 'Test',
            'first_name' => 'Test',
            'mid_name' => 'Test'
        ]);

        $response = $this->post('/register', [
            'name' => 'Test User',
            'flat' => '29',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
      //  $response->assertRedirect(RouteServiceProvider::HOME);
    }
}
