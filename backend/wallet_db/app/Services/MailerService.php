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
    $htmlBody = "<!DOCTYPE html><div style=\"display: flex; flex-direction: column; line-height: 1.6;\"><p>Your confirmation token is: <b>{$token}</b></p><p>Session ID: <b>{$session_id}</b></p></div><html>";
  
    Mail::html($htmlBody, function ($message) use ($email, $subject) {
      $message->to($email)
        ->subject($subject)
        ->from(config('mail.from.address'), config('mail.from.name'));
    });
  }
}