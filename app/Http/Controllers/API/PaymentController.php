<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ResponseFormatter;
use App\Helpers\ResponseFormatterEntity;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Cache;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use App\Jobs\ProcessTransaction;
use Illuminate\Database\Eloquent\Collection;

class PaymentController extends Controller
{

    public function CreatePayment(Request $request)
    {
        try {
            $request->validate([
                "amount" => "required"
            ]);

            $user_id = Auth::user()->id;

            //binding data transaction to variable data
            $data = [
                'user_id' => $user_id,
                'amount' => Crypt::decryptString($request->amount),
                'status' => 'pending'
            ];

            //create transaction
            $transaction = Transaction::create($data);

            //formater
            $formatter = new ResponseFormatterEntity;
            $format_transaction = $formatter->GetTransaction($transaction);

            if (is_array($format_transaction) || is_object($format_transaction)) {
                $format_transaction = json_encode($format_transaction, JSON_UNESCAPED_SLASHES);
            }

            return  ResponseFormatter::success(Crypt::encryptString($format_transaction), 'Insert Data Payment Success');
        } catch (Exception $error) {
            return ResponseFormatter::error($error->getMessage(), 'Something went wrong', 500);
        }
    }

    public function changeStatus(Request $request, $id)
    {
        $this->validateRequest($request);

        try {
            ProcessTransaction::dispatch($id, Crypt::decryptString($request->status));

            return ResponseFormatter::success(null, 'Change Status Success');
        } catch (Exception $error) {
            return ResponseFormatter::error($error->getMessage(), 'Something went wrong', 500);
        }
    }

    private function validateRequest(Request $request)
    {
        $request->validate([
            'status' => 'required'
        ]);
    }

    public function HistoryPayment()
    {
        try {
            $cacheKey = 'history_payment_' . Auth::id();
            $transaction = Cache::remember($cacheKey, now()->addMinutes(10), function () {
                return Transaction::where('user_id', Auth::user()->id)
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);
            });

            $transaction_array = $transaction->toArray();
            $transaction_json = json_encode($transaction_array);
            $transaction_encript = Crypt::encryptString($transaction_json);

            $response = ResponseFormatter::success(
                $transaction_encript, 'Transaction Histories Fetched'
            );

            return $response;

        } catch (Exception $error) {
            return ResponseFormatter::error($error->getMessage(), 'Something went wrong', 500);
        }
    }

    public function SummaryPayment()
    {
        $userId = auth()->id();
        $cacheKey = "summary_payment_$userId";

        try {
            $transactions = Transaction::query()
                ->where('user_id', $userId)
                ->get();

            $summary = $this->DataSummary($transactions);

            // Konversikan array ke JSON tanpa tanda backslash
            $summary_json = json_encode($summary, JSON_UNESCAPED_SLASHES);
            $summary_encript = Crypt::encryptString($summary_json);


            $response = ResponseFormatter::success($summary_encript, 'Summary Transaction Fetched');

            $this->cacheResponse($cacheKey, $response);

            return $response;
        } catch (Exception $error) {
            return ResponseFormatter::error($error->getMessage(), 'Something went wrong', 500);
        }
    }

    private function DataSummary(Collection $transactions)
    {
        return [
            'total_transactions' => $transactions->count(),
            'average_amount' => $transactions->avg('amount'),
            'highest_transaction' => $transactions->sortByDesc('amount')->first(),
            'lowest_transaction' => $transactions->sortBy('amount')->first(),
            'longest_name_transaction' => $this->findLongestName($transactions),
            'status_distribution' => $transactions->groupBy('status')->map->count(),
        ];
    }

    private function findLongestName(Collection $transactions)
    {
        $transactions = $transactions->map(function ($transaction) {
            $transaction->user_name = $transaction->user->name;
            return $transaction;
        });

        return $transactions->sortByDesc(function ($transaction) {
            return strlen($transaction->user_name);
        })->first();
    }

    private function cacheResponse($cacheKey, $response)
    {
        $encryptedResponse = Crypt::encryptString($response);
        Cache::put($cacheKey, json_encode($encryptedResponse), now()->addMinutes(10));

        $cachedResponse = Cache::store('redis')->get($cacheKey);

        if ($encryptedResponse === Crypt::decryptString($cachedResponse)) {
            return json_decode($cachedResponse, true);
        }
    }
}
