<?php

namespace App\Http\Controllers;

use App\Models\MerchantOrder;
use App\Models\MerchantOrdersPaymentMethod;
use App\Models\PaymentMethod;
use App\Models\TradeOrder;
use App\Models\Wallet;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TradeOrderController extends Controller
{
    //
    public function create()
    {
        try {
            //'open'/'create' open order waiting to trade fiat
            //'waiting' already sent fiat waiting coin owner confirm
            //'complete' trader and merchant recieve what they want
            //'cancel' you can cancel order when order is create only 

            $validate = request()->validate([
                'merchant_order_id' => ['required'],
                'amount' => ['required'],
                'payment_method' => ['required'],
            ]);

            //check have payment method in merchant order
            $payment_method = PaymentMethod::where('name', request()->payment_method)->get()->first();

            if (!$payment_method) return response()->json(['message' => 'payment method not in our database'], 401);

            $merchant_paymet_method = MerchantOrdersPaymentMethod::where([['payment_method_id', $payment_method->id], ['merchant_order_id', request()->merchant_order_id]])->get()->first();

            if (!$merchant_paymet_method) return response()->json(['message' => 'payment method not in merchant order'], 401);

            //amount of coins are resonable
            $merchant_order = MerchantOrder::where('id', request()->merchant_order_id)->get()->first();

            if (!$merchant_order) return response()->json(['message' => 'merchant order not found'], 401);

            if ($merchant_order->status != 'start') return response()->json(['message' => 'merchant order was closed'], 401);


            if (request()->amount > $merchant_order->available_coin) return response()->json(['message' => 'you request amount are not resonable'], 401);

            // auth_id != merchant
            // if (Auth::id() == $merchant_order->merchant_id) return response()->json(['message' => 'you can not trade order that you had created '], 401);
            if (request()->amount * $merchant_order->price < $merchant_order->lower_limit) return response()->json(['message' => 'Your amount are less than lower limit that merchant setted.'], 401);

            $order = TradeOrder::create([
                "user_id" => Auth::id(),
                "merchant_order_id" => $merchant_order->id,
                "amount" => request()->amount,
                "payment_method_id" => $payment_method->id,
                "total_price" => request()->amount * $merchant_order->price,
                "status" => "create"
            ]);

            // $trade_order = TradeOrder::with(['merchant_order', 'payment_method'])->where('id', $order->id)->get()->first();
            $trade_order = $this->trade_order($order->id);

            return response()->json($trade_order, 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function put($id)
    {
        try {
            //'waiting' type is buy trader only one can change status/type is sell merchant only one can change status
            //'complete' opposite of waiting status 'buy' merchant can change / 'sell' trader can change

            $validate = request()->validate([
                'status' => ['required']
            ]);

            //complete and waiting only
            // if (request()->status != 'waiting' && request()->status != 'complete')

            $trade_order = $this->trade_order($id);

            if (request()->status == 'waiting') {

                if ($trade_order->status != 'create') return response()->json(['message' => 'Order is not Create status'], 401);

                if ($trade_order->merchant_order->type == 'buy') {
                    if ($trade_order->user_id != Auth::id()) return response()->json(['message' => 'You do not have permission to change status'], 401);
                } else if ($trade_order->merchant_order->type == 'sell') {
                    if ($trade_order->merchant_order->merchant_id != Auth::id()) return response()->json(['message' => 'You do not have permission to change status'], 401);
                } else return response()->json(['message' => 'Merchant orde type not correct'], 401);

                $trade_order->status = request()->status;
                $trade_order->save();

                return response()->json($trade_order, 200);
            }

            if (request()->status == 'complete') {
                if ($trade_order->status != 'waiting') return response()->json(['message' => 'Order is not Waiting status'], 401);


                if ($trade_order->merchant_order->type == 'sell') {
                    if ($trade_order->user_id != Auth::id()) return response()->json(['message' => 'You do not have permission to change status'], 401);
                } else if ($trade_order->merchant_order->type == 'buy') {
                    if ($trade_order->merchant_order->merchant_id != Auth::id()) return response()->json(['message' => 'You do not have permission to change status'], 401);
                } else return response()->json(['message' => 'Merchant orde type not correct'], 401);

                $trade_order->status = request()->status;
                $trade_order->save();

                //add coin to wallet decease available coin if type is buy MUST decease in_order for merchant wallet 
                //buy merchant decease coin in wallet sell trader decease coin in wallet
                $merchant_wallet = Wallet::where([['user_id', $trade_order->merchant_order->merchant_id], ['coin_id', $trade_order->merchant_order->coin_id]])->get()->first();
                $trader_wallet = Wallet::where([['user_id', $trade_order->user_id], ['coin_id', $trade_order->merchant_order->coin_id]])->get()->first();

                if ($trade_order->merchant_order->type == 'buy') {
                    $merchant_wallet->total = $merchant_wallet->total - $trade_order->amount;
                    $merchant_wallet->in_order = $merchant_wallet->in_order - $trade_order->amount;
                    $trader_wallet->total = $trader_wallet->total + $trade_order->amount;
                } else if ($trade_order->merchant_order->type == 'sell') {
                    $merchant_wallet->total = $merchant_wallet->total + $trade_order->amount;
                    $trader_wallet->total = $trader_wallet->total - $trade_order->amount;
                }

                $trade_order->merchant_order->available_coin = $trade_order->merchant_order->available_coin - $trade_order->amount;
                $available_total = $trade_order->merchant_order->available_coin * $trade_order->merchant_order->price;

                if ($available_total < $trade_order->merchant_order->lower_limit) {
                    $trade_order->merchant_order->lower_limit = $available_total;
                }

                $merchant_wallet->save();
                $trader_wallet->save();
                $trade_order->merchant_order->save();

                return response()->json($trade_order, 200);
            }

            return response()->json(['message' => 'Status is not correct'], 401);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function show()
    {

        try {
            //filter
            // $validate = request()->validate([
            //     'type' => ['required'],
            //     'fiat' => ['required'],
            //     'coin' => ['required'],
            //     // 'payment_method' => ['required'] // arrays
            // ]);


            //search by payment method
            //search by status
            //search by type
            //search by fiat id
            //search by coin id

            $orders = TradeOrder::with(['merchant_order' => ['fiat', 'coin', 'merchant'], 'payment_method'])->where('user_id', Auth::id())
                ->whereHas('payment_method', function ($q) {
                    if (request()->payment_method) $q->where('name', request()->payment_method);
                })->whereHas('merchant_order', function ($q) {
                    if (request()->type) $q->where('type', request()->type);
                })->whereHas('merchant_order.fiat', function ($q) {
                    if (request()->fiat) $q->where([['type', 'fiat'], ['name', request()->fiat]]);
                })->whereHas('merchant_order.coin', function ($q) {
                    if (request()->coin) $q->where([['type', 'coin'], ['name', request()->coin]]);
                })->where(function ($q) {
                    if (request()->status) $q->where('status', request()->status);
                })
                ->get();

            return response()->json($orders, 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function destroy($id)
    {
        try {
            //status must be open

            $trade_order = $this->trade_order($id);

            if ($trade_order->status != 'create') return response()->json(['message' => 'Status is not correct'], 401);

            $trade_order->status = 'canceled';
            $trade_order->save();
            return response()->json($trade_order, 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    private function trade_order($id)
    {
        return TradeOrder::with(['merchant_order', 'payment_method'])->where('id', $id)->get()->first();
    }
}
