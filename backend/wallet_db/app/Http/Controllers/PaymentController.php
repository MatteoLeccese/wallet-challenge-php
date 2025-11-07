<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\InitiatePaymentRequest;
use App\Http\Requests\InitiatePurchaseRequest;
use App\Http\Requests\ConfirmPaymentRequest;
use App\Http\Requests\ConfirmPurchaseRequest;
use App\Services\PaymentsService;


class PaymentController extends Controller
{
    private PaymentsService $paymentsService;

  public function __construct(PaymentsService $paymentsService)
  {
    $this->paymentsService = $paymentsService;
  }

  /**
   * Initiate a payment
   */
  public function initiatePayment(InitiatePaymentRequest $request): JsonResponse
  {
    // Extract user ID from the authenticated user
    $userId = $request->user()->id;
    return $this->paymentsService->initiatePayment($userId, $request->validated());
  }

  /**
   * Confirm a payment
   */
  public function confirmPayment(ConfirmPaymentRequest $request): JsonResponse
  {
    return $this->paymentsService->confirmPayment($request->validated());
  }

  /**
   * Initiate a purchase
   */
  public function initiatePurchase(InitiatePurchaseRequest $request): JsonResponse
  {
    // Extract user ID from the authenticated user
    $userId = $request->user()->id;
    return $this->paymentsService->initiatePurchase($userId, $request->validated());
  }

  /**
   * Confirm a purchase
   */
  public function confirmPurchase(ConfirmPurchaseRequest $request): JsonResponse
  {
    // Extract user ID from the authenticated user
    $userId = $request->user()->id;
    return $this->paymentsService->confirmPurchase($userId, $request->validated());
  }

}
