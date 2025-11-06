<?php

namespace App\Services;

use App\Models\User;
use App\Models\Wallet;
use App\Models\Transaction;

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
      // Find the current user's wallet
      $user = User::with('wallet')->find($user_id);

      // If the user or wallet is not found we throw an error
      if (!$user || !$user->wallet) {
        return response()->json([
          'status' => 404,
          'message' => 'Balance retrieval failed',
          'error' => 'User not found or wallet not associated',
          'data' => null,
        ], 404);
      }

      // Fetch the wallet with relations
      $wallet = Wallet::with(['transactions', 'outgoingPayments', 'incomingPayments'])->find($user->wallet->id);

      // If the wallet is not found we throw an error
      if (!$wallet) {
        return response()->json([
          'status' => 404,
          'message' => 'Balance retrieval failed',
          'error' => 'Wallet not found for this user',
          'data' => null,
        ], 404);
      }

      // Return the balance and related data
      return response()->json([
        'status' => 200,
        'message' => 'Balance retrieved successfully',
        'error' => null,
        'data' => [
          'user_id' => $user->id,
          'wallet_id' => $wallet->id,
          'balance' => $wallet->balance,
          'transactions' => $wallet->transactions->map(fn ($transaction) => [
            'id' => $transaction->id,
            'type' => $transaction->type,
            'amount' => $transaction->amount,
            'reference_id' => $transaction->reference_id,
            'created_at' => $transaction->created_at,
          ]),
          'outgoing_payments' => $wallet->outgoingPayments->map(fn ($outgoing_payment) => [
            'id' => $outgoing_payment->id,
            'amount' => $outgoing_payment->amount,
            'status' => $outgoing_payment->status,
            'created_at' => $outgoing_payment->created_at,
          ]),
          'incoming_payments' => $wallet->incomingPayments->map(fn ($incoming_payment) => [
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

}
