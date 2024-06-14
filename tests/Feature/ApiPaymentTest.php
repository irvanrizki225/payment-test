<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Crypt;
use Tests\TestCase;
use App\Models\User;
use App\Models\Transaction;
use App\Jobs\ProcessTransaction;
use Illuminate\Support\Facades\Queue;

class ApiPaymentTest extends TestCase
{
    // use RefreshDatabase;

    public function test_api_create_payment()
    {
        $user = User::factory()->create();

        //auth
        $this->actingAs($user, 'api');

        //send api
        $response = $this->postJson('/api/v1/payment', ['amount' => Crypt::encryptString(20000)]);
        $response->assertStatus(200);
        sleep(3);

        //decript response data
        $data = Crypt::decryptString($response->json('data'));
        $response_data_transaction = json_decode($data, true);

        // mengambil data request
        $transaction = Transaction::find($response_data_transaction['id']);

        // Periksa apakah ada
        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
        ]);
    }

    public function test_api_change_status()
    {
        $user = User::factory()->create();

        //auth
        $this->actingAs($user, 'api');

        //send api create payment
        $response_create_payment = $this->postJson('/api/v1/payment', ['amount' => Crypt::encryptString(20000)]);
        $response_create_payment->assertStatus(200);

        //decript response data
        $data = Crypt::decryptString($response_create_payment->json('data'));
        $response_data_transaction = json_decode($data, true);

        //transaction id
        $transactionId = $response_data_transaction['id'];

        // Send API change status payment
        $responseChangeStatus = $this->postJson("/api/v1/payment/{$transactionId}/status", ['status' => Crypt::encryptString('completed')]);
        $responseChangeStatus->assertStatus(200);
        $transaction = Transaction::where('id', $transactionId)->first();

        // check data
        $this->assertEquals('completed', $transaction->status);
    }

    public function test_queue_100_payment()
    {
        Queue::fake();

        // Dispatch the job 100 times
        for ($i = 0; $i < 100; $i++) {
            $transaction = Transaction::factory()->create();
            ProcessTransaction::dispatch($transaction->id, 'completed');
        }

        // Assert that the job was pushed 100 times
        Queue::assertPushed(ProcessTransaction::class, 100);
    }

}
