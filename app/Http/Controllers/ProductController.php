<?php

namespace App\Http\Controllers;

use App\Models\PackageName;
use App\Models\Product;
use App\Models\Transaction;
use App\Services\CafeService;
use App\Services\MyketService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request, $packageName)
    {
        if (!$packageName = PackageName::with('products')->where('name', $packageName)->first()) {
            return response()->json([
                'message' => 'package name not found'
            ], 404);
        }
        $products = $packageName->products;
        return $products;
    }

    public function store(Request $request, $packageName)
    {
        $data = $request->validate([
            'title' => 'string|required',
            'price' => 'integer|required',
            'type' => ['integer', Rule::in(Product::TYPES)],
        ]);

        if (!$packageName = PackageName::with('products')->where('name', $packageName)->first()) {
            return response()->json([
                'message' => 'package name not found'
            ], 404);
        }

        $product = $packageName->products()->create($data);
        return $product;
    }

    public function update(Request $request, $packageName, $productId)
    {
        $data = $request->validate([
            'title' => 'string|required',
            'price' => 'integer|required',
            'type' => ['integer', Rule::in(Product::TYPES)],
        ]);

        if (!$packageName = PackageName::with('products')->where('name', $packageName)->first()) {
            return response()->json([
                'message' => 'package name not found'
            ], 404);
        }

        if (!$product = Product::where('id', $productId)->first()) {
            return response()->json([
                'message' => 'product not found'
            ], 404);
        }

        $product->update($data);
        return $product;
    }

    public function delete($packageName, $productId)
    {
        if (!$packageName = PackageName::with('products')->where('name', $packageName)->first()) {
            return response()->json([
                'message' => 'package name not found'
            ], 404);
        }

        if (!$product = Product::where('id', $productId)->first()) {
            return response()->json([
                'message' => 'product not found'
            ], 404);
        }

        $product->delete();
        return response()->json([
            'message' => 'product deleted'
        ]);
    }

    public function subscribe(Request $request, $packageName, $productId)
    {
        $data = $request->validate([
            'gateway' => 'string|required|in:cafe,myket',
            'purchase_token' => 'string|required',
        ]);

        if (!$packageName = PackageName::where('name', $packageName)->first()) {
            return response()->json([
                'message' => 'package name not found'
            ], 404);
        }

        $user = auth()->user();
        if (!$product = Product::with(['packageName.cafeConfig'])->where('id', $productId)->first()) {
            return response()->json([
                'message' => 'product not found'
            ], 404);
        }

        if ($data['gateway'] == 'zarinpal') {
            if (!$transaction = Transaction::where('authority', $data['authority'])->first()) {
                return response()->json([
                    'message' => 'transaction not found'
                ], 404);
            }

            if ($transaction->status == Transaction::STATUSES['consumed']) {
                return response()->json([
                    'message' => 'already consumed'
                ], 400);
            }
            $merchantId = config('zarinpal.merchant_id');
            $response = Http::retry(3, 100, throw: false)->post(config('zarinpal.url.verify'), [
                'merchant_id' => $merchantId,
                'amount' => $product['price'] * 10,
                'authority' => $data['authority'],
            ]);
            if ($response['data'] == [] || $response['data']['code'] == 101) {
                return response()->json([
                    "message" => "failed",
                    "code" => strval($response['errors']['code'] ?? 101),
                ], 400);
            }
        }

        if ($data['gateway'] == 'cafe') {
            // $service = new CafeService($product->packageName->cafeConfig);
            // $purchaseStatus = $service->checkPurchase($product, $data['purchase_token']);
            // if (!$purchaseStatus['status']) {
            //     return response()->json([
            //         'message' => $purchaseStatus['message'],
            //     ], 400);
            // }
            // $service->consumePurchase($product, $data['purchase_token']);
            $user->transactions()->create([
                'amount' => $product->price,
                'authority' => $data['purchase_token'],
                'product_id' => $product->id,
                'status' => Transaction::STATUSES['consumed'],
                'gateway' => Transaction::GATEWAYS[$data['gateway']],
            ]);
        }

        if ($data['gateway'] == 'myket') {
            $service = new MyketService($product->packageName->cafeConfig);
            $purchaseStatus = $service->checkPurchase($product, $data['purchase_token']);
            if (!$purchaseStatus['status']) {
                return response()->json([
                    'message' => $purchaseStatus['message'],
                ], 400);
            }
            $user->transactions()->create([
                'amount' => $product->price,
                'authority' => $data['purchase_token'],
                'product_id' => $product->id,
                'status' => Transaction::STATUSES['consumed'],
                'gateway' => Transaction::GATEWAYS[$data['gateway']],
            ]);
        }

        $product->buy($user, $data['purchase_token'], Transaction::GATEWAYS[$data['gateway']]);

        return response()->json([
            'message' => 'subscribed successfuly'
        ]);
    }
}
