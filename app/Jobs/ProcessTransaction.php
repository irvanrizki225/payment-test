<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Transaction;

class ProcessTransaction implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $transactionId, $request;

    /**
     * Create a new job instance.
     */
    public function __construct($transactionId, $request)
    {
        $this->transactionId = $transactionId;
        $this->request = $request;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $transaction = Transaction::find($this->transactionId);
        if ($transaction) {
            $transaction->status = $this->request;
            $transaction->udpate();
        }
    }
}
