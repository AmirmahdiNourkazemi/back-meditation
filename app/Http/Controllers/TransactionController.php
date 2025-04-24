<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Modules\Gateway\AsanPardakht\AsanPardakhtApi;
use App\Modules\Gateway\Digipay\DigiPayApi;
use App\Modules\Gateway\Zarinpal\ZarinpalApi;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate([
            'per_page' => 'integer'
        ]);

        $transactions = auth()->user()->transactions()->paginate($data['per_page'] ?? 30);

        return $transactions;
    }

    public function getGateway(Request $request)
    {
        $data = $request->validate([
            'description' => 'required|string',
            'product_id' => 'nullable|string',
            'platform' => 'in:mobile,web'
        ]);

        if (isset($data['product_id']) && !$product = Product::where('id', $data['product_id'])->first()) {
            return response()->json([
                'message' => 'product not found'
            ], 404);
        }

        $user = auth()->user();
        if ($user->products()->where('products.id', $data['product_id'])->where('type', Product::TYPES['permanent'])->first()) {
            return response()->json([
                'message' => 'already have product'
            ], 400);
        }

        $transaction = $user->transactions()->create([
            'amount' => floor($product->price * 1.1),
            'product_id' => $data['product_id'],
            'uuid' => Str::uuid()->toString()
        ]);

        $gateway = Transaction::GATEWAYS['asanpardakht'];
        $response = AsanPardakhtApi::getGateway($transaction);
        if (!($response['status'] ?? false)) {
            $response = ZarinpalApi::getGateway($transaction, $data['description']);
            $gateway = Transaction::GATEWAYS['zarinpal'];
            if (!($response['status'] ?? false)) {
                $response = DigiPayApi::getGateway($transaction);
                $gateway = Transaction::GATEWAYS['digipay'];
                if (!($response['status'] ?? false)) {
                    return response()->json(["message" => "failed"], 400);
                }
            }
       }

        $transaction->gateway = $gateway;
        $transaction->authority = $response['data']['authority'];
        $transaction->save();

        return response()->json([
            'url' => $response['data']['url']
        ]);
    }
}
