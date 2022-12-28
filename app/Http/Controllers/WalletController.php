<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function show()
    {
        $lists= Wallet::with(['user', 'coin'])->where('user_id', Auth::id())->get();
        foreach ($lists as $list) {
        }
        return response()->json(['wallet' => $lists]);
    }
}
