<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payment_sessions', function (Blueprint $table) {
            $table->id();
            $table->char('token', 6);
            $table->boolean('confirmed')->default(false);
            $table->decimal('amount', 15, 2);
            $table->unsignedBigInteger('wallet_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index('token', 'IDX_payment_sessions_token');
            $table->index('wallet_id', 'IDX_payment_sessions_walletId');
            $table->foreign('wallet_id', 'FK_payment_sessions_wallet')
                ->references('id')
                ->on('wallets')
                ->onDelete('set null')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_sessions', function (Blueprint $table) {
            $table->dropForeign('FK_payment_sessions_wallet');
        });
        Schema::dropIfExists('payment_sessions');
    }
};
