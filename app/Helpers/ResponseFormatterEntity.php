<?php

namespace App\Helpers;

class ResponseFormatterEntity
{
    public function GetUser($user){

        $data = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ];

        return $data;
    }

    public function GetTransaction($transaction)
    {
        $data = [
            'id' => $transaction->id,
            'user' => $transaction->user?->name,
            'amount' => $transaction->amount,
            'status' => $transaction->status,
        ];

        return $data;
    }
}
