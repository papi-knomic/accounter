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
	    $account = $accountEntry->account;
	    $type = $accountEntry->type;

	    if ($type === AccountEntry::DEBIT) {
		    $account->balance -= $accountEntry->amount;
	    } else {
		    $account->balance += $accountEntry->amount;
	    }

	    $account->transaction_count++;

	    // Save the updated account
	    $account->save();
    }

    /**
     * Handle the AccountEntry "updated" event.
     */
    public function updated(AccountEntry $accountEntry): void
    {
	    $originalAmount = $accountEntry->getOriginal('amount');
	    $newAmount = $accountEntry->amount;
	    $type = $accountEntry->type;
	    $account = $accountEntry->account;

	    // Check if the amount has been altered
	    if ($originalAmount != $newAmount) {
			if ($type === AccountEntry::DEBIT) {
				$account->balance += $originalAmount;
				$account->balance -= $newAmount;
			} else {
				$account->balance -= $originalAmount;
				$account->balance += $newAmount;
			}
	    }
	    $account->save();
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
