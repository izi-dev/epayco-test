<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\SendCodePayNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class PayWalletTest extends TestCase
{
    use RefreshDatabase;

    public function test_pay_wallet_successful()
    {
        Notification::fake();

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

        $response = $this->postJson(route('pay-wallet'), [
            'document' => '9999999999',
            'phone_number' => '99999999',
            'amount' => '20000'
        ]);

        $response->assertStatus(200);
        Notification::assertSentTo(
            [User::first()], SendCodePayNotification::class
        );
        $this->assertDatabaseCount('transactions', 2);
        $this->assertDatabaseHas('transactions', [
            'amount' => 20000,
            'wallet_id' => User::first()->wallet->id,
            'status' => 'pending',
            'type' => 'pay'
        ]);
    }

    public function test_pay_wallet_request_required_document()
    {
        $response = $this->postJson(route('pay-wallet'), [
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

    public function test_pay_wallet_request_required_amount()
    {
        $response = $this->postJson(route('pay-wallet'), [
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

    public function test_pay_wallet_request_required_phone_number()
    {
        $response = $this->postJson(route('pay-wallet'), [
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

    public function test_pay_wallet_successful_inssuficient_balance()
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

        $response = $this->postJson(route('pay-wallet'), [
            'document' => '9999999999',
            'phone_number' => '99999999',
            'amount' => '50000'
        ]);

        $response->assertStatus(422);
        $response
            ->assertJson(fn (AssertableJson $json) =>
            $json
                ->where('message', 'insufficient balance.')
                ->etc()
            );
    }
}
