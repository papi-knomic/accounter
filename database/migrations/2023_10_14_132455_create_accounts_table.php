<?php

use App\Models\Account;
use App\Models\User;
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
        Schema::create(Account::TABLE_NAME, function (Blueprint $table) {
            $table->id();
			$table->uuid();
			$table->string(Account::ACCOUNT_NAME);
			$table->float(Account::BALANCE, 10, 2)->default(0);
			$table->integer(Account::TRANSACTION_COUNT)->default(0);
	        $table->foreignId(Account::USER_ID)->references(User::ID)->on(User::TABLE_NAME)->onDelete('cascade');
	        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
