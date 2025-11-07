<?php

namespace App\Services;

use App\Models\User;
use App\Models\Wallet;
use App\Models\Transaction;
use App\Models\PaymentSession;

/**
 * WalletService handles wallet operations such as top-up.
 */
class WalletService
{
  /**
   * Top-up wallet by document and phone.
   *
   * @param array $request
   */
  public function topUp(array $request)
  {
    try {
      // Find user by document and phone
      $user = User::where('document', $request['document'])
        ->where('phone', $request['phone'])
        ->with('wallet')
        ->first();

      if (!$user || !$user->wallet) {
        return response()->json([
          'message' => 'Wallet top-up failed',
          'error' => 'User not found with given document and phone',
        ], 404);
      }

      // Update wallet balance
      $wallet = $user->wallet;
      $wallet->balance = number_format((float)$wallet->balance + $request['amount'], 2, '.', '');
      $wallet->save();

      // Register transaction
      $transaction = Transaction::create([
        'type' => Transaction::TYPE_TOP_UP,
        'amount' => number_format($request['amount'], 2, '.', ''),
        'wallet_id' => $wallet->id,
      ]);

      return response()->json([
        'message' => 'Wallet recharged successfully',
        'data' => [
          'user_id' => $user->id,
          'wallet_id' => $wallet->id,
          'new_balance' => $wallet->balance,
          'transaction_id' => $transaction->id,
        ]
      ], 200);

    } catch (\Throwable $th) {
      return response()->json([
        'error' => $th->getMessage() ?? 'Unexpected error during wallet top-up',
        'message' => 'Wallet top-up failed',
      ], 400);
    }
  }

  /**
   * Get wallet balance and related data for the authenticated user.
   *
   * @param int $user_id
   * @return \Illuminate\Http\JsonResponse
   */
  public function getBalance(int $user_id)
  {
    try {
      $user = User::with('wallet')->find($user_id);

      if (!$user || !$user->wallet) {
        return response()->json([
          'status' => 404,
          'message' => 'Balance retrieval failed',
          'error' => 'User not found or wallet not associated',
          'data' => null,
        ], 404);
      }

      $wallet = Wallet::with([
        'transactions',
        'outgoingPayments' => fn ($query) => $query->where('status', 'PENDING'),
        'incomingPayments' => fn ($query) => $query->where('status', 'PENDING'),
      ])->find($user->wallet->id);

      if (!$wallet) {
        return response()->json([
          'status' => 404,
          'message' => 'Balance retrieval failed',
          'error' => 'Wallet not found for this user',
          'data' => null,
        ], 404);
      }

      $availableBalance = $this->getAvailableBalance($wallet->id);
      $pendingBalance = $this->getPendingBalance($wallet->id);

      return response()->json([
        'status' => 200,
        'message' => 'Balance retrieved successfully',
        'error' => null,
        'data' => [
          'user_id' => $user->id,
          'wallet_id' => $wallet->id,
          'balance' => $wallet->balance,
          'available_balance' => number_format($availableBalance, 2, '.', ''),
          'pending_balance' => number_format($pendingBalance, 2, '.', ''),
          'transactions' => $wallet->transactions->map(fn ($transaction) => [
            'id' => $transaction->id,
            'type' => $transaction->type,
            'amount' => $transaction->amount,
            'reference_id' => $transaction->reference_id,
            'created_at' => $transaction->created_at,
          ]),
          'outgoingPayments' => $wallet->outgoingPayments->map(fn ($outgoing_payment) => [
            'id' => $outgoing_payment->id,
            'amount' => $outgoing_payment->amount,
            'status' => $outgoing_payment->status,
            'created_at' => $outgoing_payment->created_at,
          ]),
          'incomingPayments' => $wallet->incomingPayments->map(fn ($incoming_payment) => [
            'id' => $incoming_payment->id,
            'amount' => $incoming_payment->amount,
            'status' => $incoming_payment->status,
            'created_at' => $incoming_payment->created_at,
          ]),
        ],
      ], 200);

    } catch (\Throwable $th) {
      return response()->json([
        'status' => 500,
        'message' => 'Balance retrieval failed',
        'error' => $th->getMessage() ?? 'Unexpected error during balance retrieval',
        'data' => null,
      ], 500);
    }
  }

  /**
   * Check available balance considering pending payments.
   *
   * @param int $walletId
   * @return float
   */
  public function getAvailableBalance(int $walletId): float
  {
    $wallet = Wallet::find($walletId);
    if (!$wallet) {
      return 0.0;
    }

    $balance = floatval($wallet->balance);

    $pending = PaymentSession::query()
      ->where('from_wallet_id', $walletId)
      ->where('status', PaymentSession::STATUS_PENDING)
      ->sum('amount');

    return $balance - floatval(value: $pending);
  }

  /**
   * Get total pending balance from payment sessions.
   *
   * @param int $walletId
   * @return float
   */
  public function getPendingBalance(int $walletId): float
  {
    return PaymentSession::query()
      ->where('from_wallet_id', $walletId)
      ->where('status', PaymentSession::STATUS_PENDING)
      ->sum('amount');
  }

}
