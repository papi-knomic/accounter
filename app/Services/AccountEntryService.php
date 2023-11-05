<?php

namespace App\Services;

use App\Models\AccountEntry;
use Carbon\Carbon;

class AccountEntryService
{

	public static function create( string $description, string $type, float $amount, int $accountID, $date = '' ) : AccountEntry
	{
		if (empty($date)) {
			$date = Carbon::now()->toDateTimeString();
		}
		$data = [
			AccountEntry::DESCRIPTION => $description,
			AccountEntry::TYPE => $type,
			AccountEntry::AMOUNT => $amount,
			AccountEntry::ACCOUNT_ID => $accountID,
			AccountEntry::DATE => $date
		];

		return AccountEntry::create($data);
	}
}