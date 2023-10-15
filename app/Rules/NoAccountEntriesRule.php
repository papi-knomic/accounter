<?php

namespace App\Rules;

use App\Models\AccountEntry;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NoAccountEntriesRule implements Rule
{
	protected $accountId;

	public function __construct($accountId)
	{
		$this->accountId = $accountId;
	}

	public function passes($attribute, $value): bool
	{
		// Check if there are entries for the account in the account_entries table
		return !AccountEntry::where('account_id', $this->accountId)->exists();
	}

	public function message(): string
	{
		return 'Balance cannot be edited because there are entries in the account entries table.';
	}
}
