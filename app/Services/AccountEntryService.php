<?php

namespace App\Services;

use App\Models\AccountEntry;

class AccountEntryService
{

	public static function create( string $description, string $type, float $amount, int $accountID ) : AccountEntry
	{
		$data = [
			AccountEntry::DESCRIPTION => $description,
			AccountEntry::TYPE => $type,
			AccountEntry::AMOUNT => $amount,
			AccountEntry::ACCOUNT_ID => $accountID
		];

		return AccountEntry::create($data);
	}
}