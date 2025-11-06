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
            $table->char('token', 6)->index();
            $table->enum('status', ['PENDING', 'COMPLETED', 'FAILED'])->default('PENDING');
            $table->decimal('amount', 15, 2);
            $table->unsignedBigInteger('from_wallet_id')->nullable();
            $table->foreign('from_wallet_id', 'FK_payment_sessions_from_wallet')
                ->references('id')
                ->on('wallets')
                ->onDelete('set null')
                ->onUpdate('cascade');
            $table->unsignedBigInteger('to_wallet_id')->nullable();
            $table->foreign('to_wallet_id', 'FK_payment_sessions_to_wallet')
                ->references('id')
                ->on('wallets')
                ->onDelete('set null')
                ->onUpdate('cascade');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_sessions', function (Blueprint $table) {
            $table->dropForeign('FK_payment_sessions_from_wallet');
            $table->dropForeign('FK_payment_sessions_to_wallet');
        }); 
        Schema::dropIfExists('payment_sessions');
    }
};
