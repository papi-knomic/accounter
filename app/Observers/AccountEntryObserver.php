<?php

namespace App\Observers;

use App\Models\AccountEntry;

class AccountEntryObserver
{
    /**
     * Handle the AccountEntry "created" event.
     */
    public function created(AccountEntry $accountEntry): void
    {
        //
    }

    /**
     * Handle the AccountEntry "updated" event.
     */
    public function updated(AccountEntry $accountEntry): void
    {
        //
    }

    /**
     * Handle the AccountEntry "deleted" event.
     */
    public function deleted(AccountEntry $accountEntry): void
    {
	    $account = $accountEntry->account;
	    $type = $accountEntry->type;

	    // Check if the type is debit
	    if ($type === AccountEntry::DEBIT) {
		    // Add the entry amount back to the account
		    $account->balance += $accountEntry->amount;
	    } else {
		    // Handle the opposite case (credit) by subtracting the entry amount
		    $account->balance -= $accountEntry->amount;
	    }

	    // Decrement account 'transaction_count'
	    $account->transaction_count--;

	    // Save the updated account
	    $account->save();
    }

    /**
     * Handle the AccountEntry "restored" event.
     */
    public function restored(AccountEntry $accountEntry): void
    {
        //
    }

    /**
     * Handle the AccountEntry "force deleted" event.
     */
    public function forceDeleted(AccountEntry $accountEntry): void
    {
        //
    }
}
