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
}
