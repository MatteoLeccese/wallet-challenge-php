<?php

namespace App\Services;

use App\Models\User;
use App\Models\Wallet;
use App\Models\PaymentSession;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\MailerService;
use App\Services\WalletService;

class PaymentsService
{
  private MailerService $mailerService;
  private WalletService  $walletService;
  private const TOKEN_EXP_MINUTES = 5;

  public function __construct(MailerService $mailerService, WalletService $walletService)
  {
    $this->mailerService = $mailerService;
    $this->walletService = $walletService;
  }

  /**
   * Generate a 6-digit numeric token as string.
   */
  private function generateToken(): string
  {
    return strval(random_int(100000, 999999));
  }

  /**
   * Compute expiration date by adding minutes to current time.
   */
  private function computeExpiration(int $minutes): string
  {
    return now()->addMinutes($minutes);
  }

  // /**
  //  * Initiate a payment session.
  //  */
  // public function initiatePayment(int $userId, array $request)
  // {
  //   try {
  //     $toDocument = $request['toDocument'] ?? null;
  //     $toPhone = $request['toPhone'] ?? null;
  //     $amount = floatval($request['amount'] ?? 0);

  //     // Find payer
  //     $fromUser = User::with('wallet')->find($userId);
  //     if (!$fromUser || !$fromUser->wallet) {
  //       return response()->json([
  //         'status' => 404,
  //         'message' => 'Payment initiation failed',
  //         'error' => 'Payer wallet not found',
  //         'data' => null,
  //       ], 404);
  //     }

  //     // Find receiver
  //     $toUser = User::with('wallet')
  //       ->where('document', $toDocument)
  //       ->where('phone', $toPhone)
  //       ->first();

  //     if (!$toUser || !$toUser->wallet) {
  //       return response()->json([
  //         'status' => 404,
  //         'message' => 'Payment initiation failed',
  //         'error' => 'Receiver not found with given document and phone',
  //         'data' => null,
  //       ], 404);
  //     }

  //     // Validate amount
  //     $fromBalance = floatval($fromUser->wallet->balance);
  //     if ($amount <= 0) {
  //       return response()->json([
  //         'status' => 400,
  //         'message' => 'Payment initiation failed',
  //         'error' => 'Amount must be greater than zero',
  //         'data' => null,
  //       ], 400);
  //     }
  //     if ($fromBalance < $amount) {
  //       return response()->json([
  //         'status' => 400,
  //         'message' => 'Payment initiation failed',
  //         'error' => 'Insufficient balance',
  //         'data' => null,
  //       ], 400);
  //     }

  //     // Create session
  //     $token = $this->generateToken();
  //     $session = PaymentSession::create([
  //       'from_wallet_id' => $fromUser->wallet->id,
  //       'to_wallet_id'   => $toUser->wallet->id,
  //       'amount'         => number_format($amount, 2, '.', ''),
  //       'status'         => PaymentSession::STATUS_PENDING,
  //       'token'          => $token,
  //       'expires_at'     => $this->computeExpiration(self::TOKEN_EXP_MINUTES),
  //     ]);

  //     // Send email
  //     $this->mailerService->sendPaymentToken($fromUser->email, $token, $session->id);

  //     return response()->json([
  //       'status' => 200,
  //       'message' => 'Payment session created and token sent to the registered email',
  //       'error' => null,
  //       'data' => [
  //         'session_id' => $session->id,
  //         'expires_at' => $session->expires_at,
  //       ],
  //     ], 200);

  //   } catch (\Throwable $th) {
  //     Log::error('Payment initiation error: ' . $th->getMessage());
  //     return response()->json([
  //       'status' => 500,
  //       'message' => 'Payment initiation failed',
  //       'error' => $th->getMessage(),
  //       'data' => null,
  //     ], 500);
  //   }
  // }

  /**
   * Initiate a payment session.
   */
  public function initiatePayment(int $userId, array $request)
  {
    try {
      $toDocument = $request['toDocument'] ?? null;
      $toPhone    = $request['toPhone'] ?? null;
      $amount     = floatval($request['amount'] ?? 0);

      // Find payer
      $fromUser = User::with('wallet')->find($userId);
      if (!$fromUser || !$fromUser->wallet) {
        return response()->json([
          'status' => 404,
          'message' => 'Payment initiation failed',
          'error' => 'Payer wallet not found',
          'data' => null,
        ], 404);
      }

      // Find receiver
      $toUser = User::with('wallet')
        ->where('document', $toDocument)
        ->where('phone', $toPhone)
        ->first();

      if (!$toUser || !$toUser->wallet) {
        return response()->json([
          'status' => 404,
          'message' => 'Payment initiation failed',
          'error' => 'Receiver not found with given document and phone',
          'data' => null,
        ], 404);
      }

      // Validate amount and available balance
      $available = $available = $this->walletService->getAvailableBalance($fromUser->wallet->id);

      if ($amount <= 0) {
        return response()->json([
          'status' => 400,
          'message' => 'Payment initiation failed',
          'error' => 'Amount must be greater than zero',
          'data' => null,
        ], 400);
      }

      if ($available < $amount) {
        return response()->json([
          'status' => 400,
          'message' => 'Payment initiation failed',
          'error' => "Insufficient balance, you have $available available (considering pending sessions).",
          'data' => null,
        ], 400);
      }

      // Create session
      $token = $this->generateToken();
      $session = PaymentSession::create([
        'from_wallet_id' => $fromUser->wallet->id,
        'to_wallet_id'   => $toUser->wallet->id,
        'amount'         => number_format($amount, 2, '.', ''),
        'status'         => PaymentSession::STATUS_PENDING,
        'token'          => $token,
        'expires_at'     => $this->computeExpiration(self::TOKEN_EXP_MINUTES),
      ]);

      // Send email
      $this->mailerService->sendPaymentToken($fromUser->email, $token, $session->id);

      return response()->json([
        'status' => 200,
        'message' => 'Payment session created and token sent to the registered email',
        'error' => null,
        'data' => [
          'session_id' => $session->id,
          'expires_at' => $session->expires_at,
        ],
      ], 200);

    } catch (\Throwable $th) {
      Log::error('Payment initiation error: ' . $th->getMessage());
      return response()->json([
        'status' => 500,
        'message' => 'Payment initiation failed',
        'error' => $th->getMessage(),
        'data' => null,
      ], 500);
    }
  }

  /**
   * Confirm a payment session.
   */
  public function confirmPayment(array $request)
  {
    try {
      $sessionId = $request['sessionId'] ?? null;
      $token = $request['token'] ?? null;

      $session = PaymentSession::query()
        ->where('id', $sessionId)
        ->where('token', $token)
        ->with(['fromWallet','toWallet'])
        ->first();

      if (!$session) {
        return response()->json([
          'status' => 404,
          'message' => 'Payment confirmation failed',
          'error' => 'Payment session not found',
          'data' => null,
        ], 404);
      }

      if ($session->status !== PaymentSession::STATUS_PENDING) {
        return response()->json([
          'status' => 400,
          'message' => 'Payment confirmation failed',
          'error' => 'Payment session is not pending',
          'data' => null,
        ], 400);
      }

      if (!$session->expires_at || now()->greaterThan($session->expires_at)) {
        $session->status = PaymentSession::STATUS_FAILED;
        $session->save();
        return response()->json([
          'status' => 400,
          'message' => 'Payment confirmation failed',
          'error' => 'Payment session has expired',
          'data' => null,
        ], 400);
      }

      if ($token !== $session->token) {
        return response()->json([
          'status' => 403,
          'message' => 'Payment confirmation failed',
          'error' => 'Invalid token',
          'data' => null,
        ], 403);
      }

      $fromWallet = Wallet::find($session->from_wallet_id);
      $toWallet   = Wallet::find($session->to_wallet_id);

      if (!$fromWallet || !$toWallet) {
        return response()->json([
          'status' => 404,
          'message' => 'Payment confirmation failed',
          'error' => 'Wallets involved in payment not found',
          'data' => null,
        ], 404);
      }

      $amount = floatval($session->amount);
      if ($fromWallet->balance < $amount) {
        $session->status = PaymentSession::STATUS_FAILED;
        $session->save();
        return response()->json([
          'status' => 403,
          'message' => 'Payment confirmation failed',
          'error' => 'Insufficient balance at confirmation',
          'data' => null,
        ], 403);
      }

      DB::transaction(function () use ($fromWallet, $toWallet, $amount, $session) {
        // Update balances
        $fromWallet->balance = number_format($fromWallet->balance - $amount, 2, '.', '');
        $toWallet->balance   = number_format($toWallet->balance + $amount, 2, '.', '');
        $fromWallet->save();
        $toWallet->save();

        // Register transactions
        Transaction::create([
          'type' => Transaction::TYPE_PAYMENT,
          'amount' => number_format($amount, 2, '.', ''),
          'wallet_id' => $fromWallet->id,
          'reference_id' => $session->id,
        ]);
        Transaction::create([
          'type' => Transaction::TYPE_DEBIT,
          'amount' => number_format($amount, 2, '.', ''),
          'wallet_id' => $fromWallet->id,
          'reference_id' => $session->id,
        ]);
        Transaction::create([
          'type' => Transaction::TYPE_CREDIT,
          'amount' => number_format($amount, 2, '.', ''),
          'wallet_id' => $toWallet->id,
          'reference_id' => $session->id,
        ]);

        // Update session status
        $session->status = PaymentSession::STATUS_COMPLETED;
        $session->save();
      });

      return response()->json([
        'status' => 200,
        'message' => 'Payment confirmed successfully',
        'error' => null,
        'data' => [
          'session_id' => $session->id,
          'amount' => number_format($amount, 2, '.', ''),
          'from_wallet_id' => $fromWallet->id,
          'to_wallet_id' => $toWallet->id,
        ],
      ], 200);

    } catch (\Throwable $th) {
      Log::error('Payment confirmation error: ' . $th->getMessage());
      return response()->json([
        'status' => 500,
        'message' => 'Payment confirmation failed',
        'error' => $th->getMessage(),
        'data' => null,
      ], 500);
    }
  }

  /**
   * Initiate a purchase session.
   */
  public function initiatePurchase(int $userId, array $request)
  {
    try {
      $amount = floatval($request['amount']);

      // Find user and wallet
      $user = User::with('wallet')->find($userId);
      if (!$user || !$user->wallet) {
        return response()->json([
          'status' => 404,
          'message' => 'Purchase initiation failed',
          'error' => 'Wallet not found',
          'data' => null,
        ], 404);
      }

      // Validate amount and available balance
      $available = $available = $this->walletService->getAvailableBalance($user->wallet->id);

      if ($amount <= 0) {
        return response()->json([
          'status' => 400,
          'message' => 'Purchase initiation failed',
          'error' => 'Amount must be greater than zero',
          'data' => null,
        ], 400);
      }

      if ($available < $amount) {
        return response()->json([
          'status' => 400,
          'message' => 'Purchase initiation failed',
          'error' => "Insufficient balance, you have $available available (considering pending sessions).",
          'data' => null,
        ], 400);
      }

      // Generate token and create session
      $token = $this->generateToken();
      $session = PaymentSession::create([
        'from_wallet_id' => $user->wallet->id,
        'to_wallet_id'   => null,
        'amount'         => number_format($amount, 2, '.', ''),
        'status'         => PaymentSession::STATUS_PENDING,
        'token'          => $token,
        'expires_at'     => $this->computeExpiration(self::TOKEN_EXP_MINUTES),
      ]);

      // Send email with token
      $this->mailerService->sendPaymentToken($user->email, $token, $session->id);

      return response()->json([
        'status' => 200,
        'message' => 'Purchase session created and token sent to your email',
        'error' => null,
        'data' => [
          'session_id' => $session->id,
          'expires_at' => $session->expires_at,
        ],
      ], 200);

    } catch (\Throwable $th) {
      return response()->json([
        'status' => 500,
        'message' => 'Purchase initiation failed',
        'error' => $th->getMessage(),
        'data' => null,
      ], 500);
    }
  }

  // public function initiatePurchase(int $userId, array $request)
  // {
  //   try {
  //     $amount = floatval($request['amount'] ?? 0);

  //     // Find user and wallet
  //     $user = User::with('wallet')->find($userId);
  //     if (!$user || !$user->wallet) {
  //       return response()->json([
  //         'status' => 404,
  //         'message' => 'Purchase initiation failed',
  //         'error' => 'Wallet not found',
  //         'data' => null,
  //       ], 404);
  //     }

  //     // Validate amount and balance
  //     $balance = floatval($user->wallet->balance);
  //     if ($amount <= 0) {
  //       return response()->json([
  //         'status' => 400,
  //         'message' => 'Purchase initiation failed',
  //         'error' => 'Amount must be greater than zero',
  //         'data' => null,
  //       ], 400);
  //     }
  //     if ($balance < $amount) {
  //       return response()->json([
  //         'status' => 400,
  //         'message' => 'Purchase initiation failed',
  //         'error' => 'Insufficient balance',
  //         'data' => null,
  //       ], 400);
  //     }

  //     // Generate token and create session
  //     $token = $this->generateToken();
  //     $session = PaymentSession::create([
  //       'from_wallet_id' => $user->wallet->id,
  //       'to_wallet_id'   => null,
  //       'amount'         => number_format($amount, 2, '.', ''),
  //       'status'         => PaymentSession::STATUS_PENDING,
  //       'token'          => $token,
  //       'expires_at'     => $this->computeExpiration(self::TOKEN_EXP_MINUTES),
  //     ]);

  //     // Send email with token
  //     $this->mailerService->sendPaymentToken($user->email, $token, $session->id);

  //     return response()->json([
  //       'status' => 200,
  //       'message' => 'Purchase session created and token sent to your email',
  //       'error' => null,
  //       'data' => [
  //         'session_id' => $session->id,
  //         'expires_at' => $session->expires_at,
  //       ],
  //     ], 200);

  //   } catch (\Throwable $th) {
  //     return response()->json([
  //       'status' => 500,
  //       'message' => 'Purchase initiation failed',
  //       'error' => $th->getMessage(),
  //       'data' => null,
  //     ], 500);
  //   }
  // }

  /**
   * Confirm a purchase session.
   */
  public function confirmPurchase(int $userId, array $request)
  {
    try {
      $sessionId = $request['sessionId'] ?? null;
      $token = $request['token'] ?? null;

      $session = PaymentSession::with(['fromWallet.user'])->find($sessionId);
      if (!$session) {
        return response()->json([
          'status' => 404,
          'message' => 'Purchase confirmation failed',
          'error' => 'Purchase session not found',
          'data' => null,
        ], 404);
      }

      if ($session->status !== PaymentSession::STATUS_PENDING) {
        return response()->json([
          'status' => 400,
          'message' => 'Purchase confirmation failed',
          'error' => 'Session not pending',
          'data' => null,
        ], 400);
      }

      if (!$session->expires_at || now()->greaterThan($session->expires_at)) {
        $session->status = PaymentSession::STATUS_FAILED;
        $session->save();
        return response()->json([
          'status' => 400,
          'message' => 'Purchase confirmation failed',
          'error' => 'Purchase session expired',
          'data' => null,
        ], 400);
      }

      if ($token !== $session->token) {
        return response()->json([
          'status' => 403,
          'message' => 'Purchase confirmation failed',
          'error' => 'Invalid token',
          'data' => null,
        ], 403);
      }

      if (!$session->fromWallet || $session->fromWallet->user->id !== $userId) {
        return response()->json([
          'status' => 403,
          'message' => 'Purchase confirmation failed',
          'error' => 'This purchase does not belong to the current user',
          'data' => null,
        ], 403);
      }

      $wallet = Wallet::find($session->from_wallet_id);
      if (!$wallet) {
        return response()->json([
          'status' => 404,
          'message' => 'Purchase confirmation failed',
          'error' => 'Wallet not found',
          'data' => null,
        ], 404);
      }

      $balance = floatval($wallet->balance);
      $amount  = floatval($session->amount);
      if ($balance < $amount) {
        $session->status = PaymentSession::STATUS_FAILED;
        $session->save();
        return response()->json([
          'status' => 400,
          'message' => 'Purchase confirmation failed',
          'error' => 'Insufficient balance',
          'data' => null,
        ], 400);
      }

      // Deduct balance and save
      $wallet->balance = number_format($balance - $amount, 2, '.', '');
      $wallet->save();

      // Register transaction
      Transaction::create([
        'type' => Transaction::TYPE_PURCHASE,
        'amount' => number_format($amount, 2, '.', ''),
        'wallet_id' => $wallet->id,
        'reference_id' => $session->id,
      ]);

      // Update session status
      $session->status = PaymentSession::STATUS_COMPLETED;
      $session->save();

      return response()->json([
        'status' => 200,
        'message' => 'Purchase confirmed successfully',
        'error' => null,
        'data' => [
          'session_id' => $session->id,
          'amount' => number_format($amount, 2, '.', ''),
          'wallet_id' => $wallet->id,
          'new_balance' => $wallet->balance,
        ],
      ], 200);

    } catch (\Throwable $th) {
      return response()->json([
        'status' => 500,
        'message' => 'Purchase confirmation failed',
        'error' => $th->getMessage(),
        'data' => null,
      ], 500);
    }
  }
}