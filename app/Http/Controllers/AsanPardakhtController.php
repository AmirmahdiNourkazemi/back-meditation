<?php

namespace App\Http\Controllers;

use App\Helpers\RedirectLinkHelper;
use App\Models\Transaction;
use App\Modules\Gateway\AsanPardakht\AsanPardakhtApi;
use Illuminate\Http\Request;

class AsanPardakhtController extends Controller
{
    public function pay(Request $request)
    {
        $data = $request->validate([
            'token' => 'string|required',
            'mobile' => 'string',
        ]);

        return view('ap-form', ['token' => $data['token'], 'mobile' => $data['mobile']]);
    }

    public function checkTransaction(Request $request)
    {
        $data = $request->validate([
            'localInvoiceId' => 'string|required',
            'platform' => 'string'
        ]);

        if (!$transaction = Transaction::with(['product.packageName', 'user'])->where('status', Transaction::STATUSES['pending'])->where('id', $data['localInvoiceId'])->first()) {
            return view('status', [
                'status' => false,
                'redirect_link' => '#'
            ]);
        }
        $redirectLink = RedirectLinkHelper::get($transaction->product->packageName->name, $data['platform'] ?? null);
        $response = AsanPardakhtApi::checkTransaction($transaction);

        if (!$response['status']) {
            return view('status', [
                'status' => false,
                'redirect_link' => $redirectLink
            ]);
        }

        $transaction->update(['status' => Transaction::STATUSES['success']]);

        $transaction->product->buy($transaction->user, $transaction['authority'], Transaction::GATEWAYS['asanpardakht']);

        return view('status', [
            'status' => true,
            'redirect_link' => $redirectLink
        ]);
    }
}
