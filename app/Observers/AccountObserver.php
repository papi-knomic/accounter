<?php

namespace App\Observers;

use App\Models\Account;
use App\Models\AccountEntry;
use App\Services\AccountEntryService;

class AccountObserver
{
    /**
     * Handle the Account "created" event.
     */
    public function created(Account $account): void
    {
        $balance = $account[Account::BALANCE];

		if ($balance > 0) {
			$description = 'Initial Balance';
			AccountEntryService::create($description, AccountEntry::CREDIT, $balance, $account->id);
			$account->transaction_count++;
			$account->save();
		}
    }

    /**
     * Handle the Account "updated" event.
     */
    public function updated(Account $account): void
    {
        $transactionCount = $account[Account::TRANSACTION_COUNT];
	    $balance = $account[Account::BALANCE];

	    if ($transactionCount == 0 && $balance > 0) {
		    $description = 'Initial Balance';
		    AccountEntryService::create($description, AccountEntry::CREDIT, $balance, $account->id);
		    $account->transaction_count++;
		    $account->save();
	    }
    }

    /**
     * Handle the Account "deleted" event.
     */
    public function deleted(Account $account): void
    {
	    $account->entries()->delete();
    }

    /**
     * Handle the Account "restored" event.
     */
    public function restored(Account $account): void
    {
        //
    }

    /**
     * Handle the Account "force deleted" event.
     */
    public function forceDeleted(Account $account): void
    {
        //
    }
}
