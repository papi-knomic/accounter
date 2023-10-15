<?php

use App\Models\Account;
use App\Models\AccountEntry;
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
        Schema::create(AccountEntry::TABLE_NAME, function (Blueprint $table) {
            $table->id();
			$table->uuid();
			$table->string(AccountEntry::DESCRIPTION);
			$table->float(AccountEntry::AMOUNT, 10);
			$table->enum(AccountEntry::TYPE, AccountEntry::TYPES);
	        $table->foreignId(AccountEntry::ACCOUNT_ID)->references(Account::ID)->on(Account::TABLE_NAME)->onDelete('cascade');
	        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(AccountEntry::TABLE_NAME);
    }
};
