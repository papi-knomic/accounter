<?php

namespace App\Services;

use App\Models\AccountEntry;
use Carbon\Carbon;

class AccountEntryService
{

	public static function create( array $data ) : AccountEntry
	{
		if (empty($data[AccountEntry::DATE])) {
			$data[AccountEntry::DATE] = Carbon::now()->toDateTimeString();
		}

		return AccountEntry::create($data);
	}
}