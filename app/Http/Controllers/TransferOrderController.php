<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\TransferOrder;
use App\Models\User;
use App\Models\Wallet;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class TransferOrderController extends Controller
{
    //processing
    //success
    //failed

    public function create()
    {
        try {
            //validate
            $validate = request()->validate([
                'receiver_email' => ['required'],
                'coin' => ['required'],
                'amount' => ['required'],
            ]);

            // check currency is coin
            $coin = Currency::where('type', 'coin')->where('name', request()->coin)->get()->first();

            if (!$coin) return response()->json(['message' => 'coin does not has in database'], 401);

            // get id from receive user and IS NOT same as sender user
            $receiver = User::with('wallets')->where('email', request()->receiver_email)->get()->first();
            // $sender = User::find(Auth::id());

            if ($receiver->id == Auth::id()) return response()->json(['message' => 'You cannot send coins to yourself.'], 401);

            // check total in sender wallet
            $sender_wallet = Wallet::where('coin_id', $coin->id)->where('user_id', Auth::id())->get()->first();
            if ($sender_wallet->total - $sender_wallet->in_order < request()->amount) return response()->json(['message' => 'Your balance has not enough to send'], 401);

            // start process with create order
            $order = TransferOrder::create([
                'coin_id' => $coin->id,
                'sender_id' => Auth::id(),
                'receiver_id' => $receiver->id,
                'amount' => request()->amount,
                'status' => 'processing'
            ]);

            // return response()->json($order, 200);

            //decease amount from sender wallet
            $sender_wallet->total = $sender_wallet->total - request()->amount;
            $sender_wallet->save();

            // try to send to receiver wallet 
            $receiver_wallet = Wallet::where([['coin_id', $coin->id], ['user_id', $receiver->id]])->get()->first();
            $receiver_wallet->total = $receiver_wallet->total + request()->amount;
            $receiver_wallet->save();

            //finish process
            $order->status = 'success';
            $order->save();
            return response()->json($order, 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function show()
    {
        // receive or send
        // sort by coin or status
        $orders = TransferOrder::with(['coin', 'receiver', 'sender']);

        // if (request()->input('type') == 'sender') {
        //     $orders->where('sender_id', Auth::id());
        // } else if (request()->input('type') == 'receiver') {
        //     $orders->where('receiver_id', Auth::id());
        // } else {
        //     $orders->where('sender_id', Auth::id())->orWhere('receiver_id', Auth::id());
        // }

        $orders->where(function ($q) {
            if (request()->input('type') == 'sender') {
                $q->where('sender_id', Auth::id());
            } else if (request()->input('type') == 'receiver') {
                $q->where('receiver_id', Auth::id());
            } else {
                $q->where('sender_id', Auth::id())->orWhere('receiver_id', Auth::id());
            }
        })->where(function ($q) {
            if (request()->input('status')) {

                $q->where('status', request()->input('status'));
            }
        })->whereHas('coin', function ($q) {
            if (request()->coin) $q->where('name', request()->coin);
        });

        return response()->json($orders->get(), 200);
    }
}
