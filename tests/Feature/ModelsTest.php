<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Transaction;

class ModelsTest extends TestCase
{
    // use RefreshDatabase;


    public function test_create_user()
    {
        $user = User::factory()->create();

        $this->assertDatabaseHas(
            'users',[
                'name' => $user->name,
                'email' => $user->email,
                'password' => $user->password,
            ]
            );
    }

    public function test_update_user()
    {
        $user = User::factory()->create();

        $user->update(['name' => 'Test Unit']);

        $this->assertDatabaseHas('users', [
            'name' => 'Test Unit',
        ]);
    }

    public function test_delete_user()
    {
        $user = User::factory()->create();

        $user->delete();

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }

    //payment
    public function test_create_payment()
    {
        $transaction = Transaction::factory()->create();

        $this->assertDatabaseHas(
            'transactions',[
                'user_id' => $transaction->user_id,
                'amount' => $transaction->amount,
                'status' => $transaction->status,
            ]
            );
    }

    public function test_update_payment()
    {
        $transaction = Transaction::factory()->create();

        $transaction->update(['status' => 'pending']);

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'status' => 'pending',
        ]);
    }

    public function test_delete_payment()
    {
        $transaction = Transaction::factory()->create();

        $transaction->delete();

        $this->assertDatabaseMissing('transactions', [
            'id' => $transaction->id,
        ]);
    }


}
