<?php

use App\Models\Account;

if (!function_exists('accountBelongsToUser')) {
	/**
	 * Check if account id belongs to user
	 * @param int $account_id
	 * @return bool
	 */
	function accountBelongsToUser( int $account_id ): bool
	{
		return in_array($account_id, getUserAccountsID());
	}
}

if (!function_exists('getUserAccountsID')) {
	/**
	 * Generate Verification Code
	 * @return array
	 */
	function getUserAccountsID(): array
	{
		return auth()->user()->accounts->pluck(Account::ID)->toArray();
	}
}

if (!function_exists('isValidDate')) {
	/**
	 * Check if the date is in a valid format or a valid date range
	 */
	function isValidDate($date): bool
	{
		// Check if the date is a single date
		if (strtotime($date)) {
			return true;
		}

		// Check if the date is a valid range
		$dateRange = explode(',', $date);

		if (count($dateRange) === 2) {
			$startDate = trim($dateRange[0]);
			$endDate = trim($dateRange[1]);

			// Check if both start and end dates are valid and end date is greater than start date
			if (strtotime($startDate) && strtotime($endDate) && strtotime($startDate) < strtotime($endDate)) {
				return true;
			}
		}

		return false;
	}

}