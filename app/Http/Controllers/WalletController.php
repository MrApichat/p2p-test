<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function show()
    {
        // $lists = Wallet::all()->where('user_id', Auth::id());
        $lists= Wallet::with(['user', 'coin'])->where('user_id', Auth::id())->get();
        // $list = Wallet::all();
        foreach ($lists as $list) {
        }
        return response()->json(['wallet' => $lists]);
    }
}
