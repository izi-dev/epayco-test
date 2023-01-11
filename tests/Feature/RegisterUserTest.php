<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class RegisterUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_user_succesfull()
    {
        $response = $this->postJson(route('register-user'), [
            'document' => '9999999999',
            'full_name' => 'freddy johanes vargas ramirez',
            'phone_number' => '99999999',
            'email' => 'admin@test.co'
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('wallets', 1);
        $this->assertDatabaseHas('users', [
            'email' => 'admin@test.co',
        ]);
    }

    public function test_register_user_request_required_document()
    {
        $response = $this->postJson(route('register-user'), [
            'full_name' => 'freddy johanes vargas ramirez',
            'phone_number' => '99999999',
            'email' => 'admin@test.co'
        ]);

        $response->assertStatus(422);
        $response
            ->assertJson(fn (AssertableJson $json) =>
                $json
                    ->where('message', 'The document field is required.')
                    ->etc()
            );
        $this->assertDatabaseCount('users', 0);
    }

    public function test_register_user_request_required_full_name()
    {
        $response = $this->postJson(route('register-user'), [
            'document' => '9999999999',
            'phone_number' => '99999999',
            'email' => 'admin@test.co'
        ]);

        $response->assertStatus(422);
        $response
            ->assertJson(fn (AssertableJson $json) =>
            $json
                ->where('message', 'The full name field is required.')
                ->etc()
            );
        $this->assertDatabaseCount('users', 0);
    }

    public function test_register_user_request_required_email()
    {
        $response = $this->postJson(route('register-user'), [
            'document' => '9999999999',
            'full_name' => 'freddy johanes vargas ramirez',
            'phone_number' => '99999999',
        ]);

        $response->assertStatus(422);
        $response
            ->assertJson(fn (AssertableJson $json) =>
            $json
                ->where('message', 'The email field is required.')
                ->etc()
            );
        $this->assertDatabaseCount('users', 0);
    }

    public function test_register_user_duplicate_document()
    {
        User::factory()->create();

        $response = $this->postJson(route('register-user'), [
            'document' => User::first()->document,
            'full_name' => 'freddy johanes vargas ramirez',
            'phone_number' => '99999999',
            'email' => 'admin@test.co'
        ]);

        $response->assertStatus(422);
        $response
            ->assertJson(fn (AssertableJson $json) =>
            $json
                ->where('message', 'the field document is duplicate.')
                ->etc()
            );
        $this->assertDatabaseCount('users', 1);
    }

    public function test_register_user_duplicate_email()
    {
        User::factory()->create();

        $response = $this->postJson(route('register-user'), [
            'document' => '9999999999',
            'full_name' => 'freddy johanes vargas ramirez',
            'phone_number' => '99999999',
            'email' => User::first()->email
        ]);

        $response->assertStatus(422);
        $response
            ->assertJson(fn (AssertableJson $json) =>
            $json
                ->where('message', 'the field email is duplicate.')
                ->etc()
            );
        $this->assertDatabaseCount('users', 1);
    }

    public function test_register_user_duplicate_phone_number()
    {
        User::factory()->create();

        $response = $this->postJson(route('register-user'), [
            'document' => '9999999999',
            'full_name' => 'freddy johanes vargas ramirez',
            'phone_number' => User::first()->phone_number,
            'email' => 'admin@test.co'
        ]);

        $response->assertStatus(422);
        $response
            ->assertJson(fn (AssertableJson $json) =>
            $json
                ->where('message', 'the field phone_number is duplicate.')
                ->etc()
            );
        $this->assertDatabaseCount('users', 1);
    }
}
