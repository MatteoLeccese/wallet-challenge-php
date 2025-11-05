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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['TOP_UP', 'PURCHASE', 'PAYMENT', 'DEBIT', 'CREDIT', 'TRANSFER']);
            $table->decimal('amount', 15, 2);
            $table->unsignedBigInteger('wallet_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index('type', 'IDX_transactions_type');
            $table->index('wallet_id', 'IDX_transactions_walletId');
            $table->foreign('wallet_id', 'FK_transactions_wallet')
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
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign('FK_transactions_wallet');
        });
        Schema::dropIfExists('transactions');
    }
};
