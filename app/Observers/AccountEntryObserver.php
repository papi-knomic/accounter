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
        //
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
