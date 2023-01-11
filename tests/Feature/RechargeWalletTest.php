<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class RechargeWalletTest extends TestCase
{
    use RefreshDatabase;

    public function test_recharge_wallet_successful()
    {
        $response = $this->postJson(route('register-user'), [
            'document' => '9999999999',
            'full_name' => 'freddy johanes vargas ramirez',
            'phone_number' => '99999999',
            'email' => 'admin@test.co'
        ]);

        $response->assertStatus(200);

        $response = $this->postJson(route('recharge-wallet'), [
            'document' => '9999999999',
            'phone_number' => '99999999',
            'amount' => '20000'
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('wallets', 1);
        $this->assertDatabaseCount('transactions', 1);
        $this->assertDatabaseHas('users', [
            'email' => 'admin@test.co',
        ]);

        $this->assertDatabaseHas('transactions', [
            'amount' => 20000,
            'wallet_id' => User::first()->wallet->id
        ]);

        $this->assertDatabaseHas('wallets', [
            'amount' => 20000,
            'user_id' => User::first()->id
        ]);
    }

    public function test_recharge_wallet_request_required_document()
    {
        $response = $this->postJson(route('recharge-wallet'), [
            'phone_number' => '99999999',
            'amount' => '20000'
        ]);

        $response->assertStatus(422);
        $response
            ->assertJson(fn (AssertableJson $json) =>
            $json
                ->where('message', 'The document field is required.')
                ->etc()
            );
    }

    public function test_recharge_wallet_request_required_amount()
    {
        $response = $this->postJson(route('recharge-wallet'), [
            'document' => '9999999999',
            'phone_number' => '99999999',
        ]);

        $response->assertStatus(422);
        $response
            ->assertJson(fn (AssertableJson $json) =>
            $json
                ->where('message', 'The amount field is required.')
                ->etc()
            );
    }

    public function test_recharge_wallet_request_required_phone_number()
    {
        $response = $this->postJson(route('recharge-wallet'), [
            'document' => '9999999999',
            'amount' => '20000'
        ]);

        $response->assertStatus(422);
        $response
            ->assertJson(fn (AssertableJson $json) =>
            $json
                ->where('message', 'The phone number field is required.')
                ->etc()
            );
    }

    public function test_recharge_wallet_successful_plus()
    {
        $response = $this->postJson(route('register-user'), [
            'document' => '9999999999',
            'full_name' => 'freddy johanes vargas ramirez',
            'phone_number' => '99999999',
            'email' => 'admin@test.co'
        ]);

        $response->assertStatus(200);

        $response = $this->postJson(route('recharge-wallet'), [
            'document' => '9999999999',
            'phone_number' => '99999999',
            'amount' => '20000'
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('wallets', 1);
        $this->assertDatabaseCount('transactions', 1);
        $this->assertDatabaseHas('users', [
            'email' => 'admin@test.co',
        ]);

        $this->assertDatabaseHas('transactions', [
            'amount' => 20000,
            'wallet_id' => User::first()->wallet->id
        ]);

        $this->assertDatabaseHas('wallets', [
            'amount' => 20000,
            'user_id' => User::first()->id
        ]);

        $response = $this->postJson(route('recharge-wallet'), [
            'document' => '9999999999',
            'phone_number' => '99999999',
            'amount' => '20000'
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseCount('transactions', 2);
        $this->assertDatabaseHas('wallets', [
            'amount' => 40000,
            'user_id' => User::first()->id
        ]);
    }
}
