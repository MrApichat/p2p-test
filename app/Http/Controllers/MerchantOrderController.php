<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\MerchantOrder;
use App\Models\MerchantOrdersPaymentMethod;
use App\Models\PaymentMethod;
use App\Models\TradeOrder;
use App\Models\Wallet;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MerchantOrderController extends Controller
{
    //start
    //closed

    public function create()
    {
        try {
            // type buy or sell
            //fiat
            // coin
            // price
            // available coin
            // lower limit
            $validate = request()->validate([
                'type' => ['required'],
                'fiat' => ['required'],
                'coin' => ['required'],
                'price' => ['required'],
                'available_coin' => ['required'],
                'lower_limit' => ['required'],
                'payment_methods' => ['required'] // array
            ]);

            if (request()->type != 'buy' && request()->type != 'sell') return response()->json(['message' => 'only has buy and sell type'], 401);

            //fiat is fiat
            $fiat = Currency::where('type', 'fiat')->where('name', request()->fiat)->get()->first();

            if (!$fiat) return response()->json(['message' => 'fiat does not has in database'], 401);

            //coin is coin

            $coin = Currency::where('type', 'coin')->where('name', request()->coin)->get()->first();

            if (!$coin) return response()->json(['message' => 'coin does not has in database'], 401);

            //check available_coin is less than total when is buy type
            if (request()->type == 'buy') {
                $wallet = Wallet::where([['user_id', Auth::id()], ['coin_id', $coin->id]])->get()->first();
                if ($wallet->total - $wallet->in_order < request()->available_coin) return response()->json(['message' => 'Your balance has not enough to create order'], 401);
            }

            //lower limit is possible
            if (request()->available_coin * request()->price < request()->lower_limit) return response()->json(['message' => 'Your lower limit is impossible'], 401);

            // fiat/coin must unique by type

            if (MerchantOrder::where([['fiat_id', $fiat->id], ['coin_id', $coin->id], ['type', request()->type]])->get()->first()) return response()->json(['message' => 'You already have Order.'], 401);

            // create
            $order = MerchantOrder::create([
                "type" => request()->type,
                "fiat_id" => $fiat->id,
                "coin_id" => $coin->id,
                "merchant_id" => Auth::id(),
                "price" => request()->price,
                "available_coin" => request()->available_coin,
                "lower_limit" => request()->lower_limit,
                "status" => "start",
            ]);

            foreach (request()->payment_methods as $method) {
                $payment_method = PaymentMethod::where('name', $method)->get()->first();

                if ($payment_method) MerchantOrdersPaymentMethod::create([
                    "payment_method_id" => $payment_method->id,
                    "merchant_order_id" => $order->id
                ]);
            }

            if (request()->type == 'buy') {
                $wallet->in_order = $wallet->in_order + request()->available_coin;

                $wallet->save();
            }


            $merchant = MerchantOrder::with(['payment_methods', "fiat", "coin", "merchant"])->where('id', $order->id)->get()->first();
            return response()->json($merchant, 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }


    public function show()
    {
        try {
            //type fiat coin
            $validate = request()->validate([
                'type' => ['required'],
                'fiat' => ['required'],
                'coin' => ['required'],
                // 'payment_method' => ['required'] // array
            ]);

            if (request()->input('type') != 'buy' && request()->input('type') != 'sell') return response()->json(['message' => 'only has buy and sell type'], 401);

            //fiat is fiat
            $fiat = Currency::where('type', 'fiat')->where('name', request()->input('fiat'))->get()->first();

            if (!$fiat) return response()->json(['message' => 'fiat does not has in database'], 401);

            //coin is coin
            $coin = Currency::where('type', 'coin')->where('name', request()->input('coin'))->get()->first();

            if (!$coin) return response()->json(['message' => 'coin does not has in database'], 401);

            $merchant = MerchantOrder::with(['payment_methods', "fiat", "coin", "merchant"])->where([['type', request()->input('type')], ['fiat_id', $fiat->id], ['coin_id', $coin->id]])->get();

            return response()->json($merchant, 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function destroy($id)
    {
        //check there are trade orders are opened
        //decease  available amount from wallet coin
        //change status

        try {
            //status must be start
            $merchant = MerchantOrder::with(['payment_methods', "fiat", "coin", "merchant"])->where('id', $id)->get()->first();

            if (!$merchant) return response()->json(['message' => 'Can not find merchant order'], 401);

            if ($merchant->status != 'start') return response()->json(['message' => 'Status is not correct'], 401);

            //check there are trade orders are opened
            $trade_orders = TradeOrder::where([['merchant_order_id', $merchant->id], [function ($query) {
                $query->where('status', 'create')->orWhere('status', 'waiting');
            }]])->get()->first();
            if ($trade_orders) return response()->json(['message' => 'This order still has open trade order'], 401);

            //decease  available amount from wallet coin
            $wallet = Wallet::where([['user_id',  $merchant->merchant_id], ['coin_id', $merchant->coin_id]])->get()->first();
            if (!$wallet) return response()->json(['message' => 'Can not find wallet'], 401);
            $wallet->in_order = $wallet->in_order - $merchant->available_coin;
            $wallet->save();


            //change status
            $merchant->status = 'closed';
            $merchant->save();
            return response()->json($merchant, 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
