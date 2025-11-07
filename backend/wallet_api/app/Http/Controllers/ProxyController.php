<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ProxyService;

class ProxyController extends Controller
{
    private ProxyService $proxy;

    public function __construct(ProxyService $proxy)
    {
        $this->proxy = $proxy;
    }

    // Auth
    public function register(Request $request)
    {
        return $this->proxy->forwardRequest('POST', '/auth/register', $request->all(), $request->headers->all());
    }

    public function login(Request $request)
    {
        return $this->proxy->forwardRequest('POST', '/auth/login', $request->all(), $request->headers->all());
    }

    // Wallet
    public function topUp(Request $request)
    {
        return $this->proxy->forwardRequest('POST', '/wallet/top-up', $request->all(), $request->headers->all());
    }

    public function balance(Request $request)
    {
        return $this->proxy->forwardRequest('GET', '/wallet/balance', null, $request->headers->all());
    }

    // Payments
    public function initiatePayment(Request $request)
    {
        return $this->proxy->forwardRequest('POST', '/payments/initiate-payment', $request->all(), $request->headers->all());
    }

    public function confirmPayment(Request $request)
    {
        return $this->proxy->forwardRequest('POST', '/payments/confirm-payment', $request->all(), $request->headers->all());
    }

    public function initiatePurchase(Request $request)
    {
        return $this->proxy->forwardRequest('POST', '/payments/initiate-purchase', $request->all(), $request->headers->all());
    }

    public function confirmPurchase(Request $request)
    {
        return $this->proxy->forwardRequest('POST', '/payments/confirm-purchase', $request->all(), $request->headers->all());
    }
}
