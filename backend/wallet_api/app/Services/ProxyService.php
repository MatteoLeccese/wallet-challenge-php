<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ProxyService
{
  public function forwardRequest(
    string $method,
    string $path,
    ?array $body,
    array $incomingHeaders
  ) {
    // Get wallet-db URL and system API key from environment
    $walletDbUrl = config('app.wallet_db_url');
    $systemApiKey = config('app.wallet_db_api_key');

    // Filter problematic headers
    unset($incomingHeaders['host'], $incomingHeaders['content-length'], $incomingHeaders['connection']);

    // Add the api key
    $headers = array_merge($incomingHeaders, [
      'Content-Type' => 'application/json',
      'x-system-api-key' => $systemApiKey,
    ]);

    $response = Http::withHeaders($headers)
      ->timeout(5)
      ->send($method, $walletDbUrl . $path, [
        'json' => $body ?? [],
      ]);

    return $response->json();
  }
}
