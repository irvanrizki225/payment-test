<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;


class EncryptionController extends Controller
{
    public function encrypt(Request $request)
    {
        $data = $request->input('data');

        // Enkripsi data
        $encrypted = Crypt::encryptString($data);

        return response()->json(['encrypted' => $encrypted]);
    }

    public function decrypt(Request $request)
    {
        $encryptedData = $request->input('data');

        // Dekripsi data
        try {
            $decrypted = Crypt::decryptString($encryptedData);
        } catch (DecryptException $e) {
            return response()->json(['error' => 'Invalid encrypted data'], 400);
        }

        return response()->json(['decrypted' => $decrypted]);
    }
}
