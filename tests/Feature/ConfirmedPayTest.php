<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\User;
use App\Notifications\SendCodePayNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ConfirmedPayTest extends TestCase
{
    use RefreshDatabase;

    public function test_confirmed_pay_successful()
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

        $transaction = Transaction::query()->where('status', 'pending')->first();
        $response = $this->postJson(route('confirmed-pay'), [
            'token' => $transaction->token,
            'code' => $transaction->code
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('transactions', [
            'amount' => 20000,
            'wallet_id' => User::first()->wallet->id,
            'status' => 'confirmed',
            'type' => 'pay'
        ]);

        $this->assertDatabaseHas('wallets', [
            'amount' => 0,
            'user_id' => User::first()->id
        ]);
    }

    public function test_confirmed_pay_request_required_token()
    {
        $response = $this->postJson(route('confirmed-pay'), [
            'code' => 'XXXXXXX'
        ]);

        $response->assertStatus(422);
        $response
            ->assertJson(fn (AssertableJson $json) =>
            $json
                ->where('message', 'The token field is required.')
                ->etc()
            );
    }

    public function test_confirmed_pay_request_required_code()
    {
        $response = $this->postJson(route('confirmed-pay'), [
            'token' => 'XXXXXXX'
        ]);

        $response->assertStatus(422);
        $response
            ->assertJson(fn (AssertableJson $json) =>
            $json
                ->where('message', 'The code field is required.')
                ->etc()
            );
    }
}
