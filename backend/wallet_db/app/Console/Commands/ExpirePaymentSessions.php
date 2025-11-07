<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PaymentSession;
use Carbon\Carbon;

class ExpirePaymentSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:expire-payment-sessions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark expired pending payment sessions as failed';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get the current time
        $now = Carbon::now();

        // How many records to process in each batch
        $batchSize = 100;

        // Update all pending payment sessions that have expired
        PaymentSession::query()
            ->where('status', PaymentSession::STATUS_PENDING)
            ->where('expires_at', '<', $now)
            ->chunkById($batchSize, function ($sessions) {
                foreach ($sessions as $session) {
                    $session->status = PaymentSession::STATUS_FAILED;
                    $session->save();
                }
            });

        // Output the number of expired sessions
        $this->info("Expired sessions processed in batches of {$batchSize}.");
    }
}
