<?php

use App\Models\Account;

if (!function_exists('accountBelongsToUser')) {
	/**
	 * Generate Verification Code
	 * @param int $account_id
	 * @return bool
	 */
	function accountBelongsToUser( int $account_id ): bool
	{
		return auth()->user()->accounts->pluck(Account::ID)->contains($account_id);
	}
}