<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('payment_method')->nullable()->after('id');
            $table->string('phone')->nullable()->after('user_email');
            $table->string('mpesa_transaction_id')->nullable()->after('phone');
            $table->enum('transaction_type', ['CustomerPayBillOnline', 'BusinessPayBill', 'CustomerBuyGoods', 'AccountBalance'])->nullable()->after('mpesa_transaction_id');
            $table->string('mpesa_status')->nullable()->after('transaction_type');
            $table->string('mpesa_receipt_number')->nullable()->after('mpesa_status');
            $table->string('stripe_token')->nullable()->after('mpesa_receipt_number');
            $table->string('stripe_charge_id')->nullable()->after('stripe_token');
            $table->string('callback_url')->nullable()->after('stripe_charge_id');
            $table->text('notes')->nullable()->after('callback_url');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'payment_method',
                'phone',
                'mpesa_transaction_id',
                'transaction_type',
                'mpesa_status',
                'mpesa_receipt_number',
                'stripe_token',
                'stripe_charge_id',
                'callback_url',
                'notes',
            ]);
        });
    }
};
