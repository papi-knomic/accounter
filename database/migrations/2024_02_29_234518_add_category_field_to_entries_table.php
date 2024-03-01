<?php

use App\Models\AccountEntry;
use App\Models\Category;
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
		Schema::table(AccountEntry::TABLE_NAME, function (Blueprint $table) {
			$table->foreignId(AccountEntry::CATEGORY_ID)->default(1)->constrained(Category::TABLE_NAME)->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::table(AccountEntry::TABLE_NAME, function (Blueprint $table) {
			// Drop the foreign key column
			$table->dropForeign([AccountEntry::CATEGORY_ID]);
			$table->dropColumn(AccountEntry::CATEGORY_ID);
		});
	}
};
