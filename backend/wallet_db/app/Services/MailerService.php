<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;

/**
 * MailerService handles sending emails via Mailhog.
 */
class MailerService
{
  /**
   * Send payment confirmation token to user.
   *
   * @param string $email
   * @param string $token
   * @param int $session_id
   * @return void
   */
  public function sendPaymentToken(string $email, string $token, int $session_id): void
  {
    $subject = 'Confirm your payment';

    $textBody = "Your confirmation token is: {$token}. Session ID: {$session_id}";
    $htmlBody = "<p>Your confirmation token is: <b>{$token}</b></p><p>Session ID: {$session_id}</p>";

    Mail::raw($textBody, function ($message) use ($email, $subject, $htmlBody) {
      $message->to($email)
        ->subject($subject)
        ->from(config('mail.from.address'), config('mail.from.name'));

      // Attach HTML version
      $message->setBody($htmlBody, 'text/html');
    });
  }
}