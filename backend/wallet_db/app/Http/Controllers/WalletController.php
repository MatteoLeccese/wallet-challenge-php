<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\TopUpWalletRequest;
use App\Services\WalletService;

class WalletController extends Controller
{
    private WalletService $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    /**
     * Top-up wallet by document and phone.
     *
     * @param TopUpWalletRequest $request
     */
    public function topUp(TopUpWalletRequest $request)
    {
        return $this->walletService->topUp($request->validated());
    }

    // Balance endpoint will be implemented later
}
