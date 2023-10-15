<?php

namespace App\Providers;

use App\Models\Account;
use App\Models\AccountEntry;
use App\Models\User;
use App\Observers\AccountEntryObserver;
use App\Observers\AccountObserver;
use App\Observers\UserObserver;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
	    Schema::defaultStringLength(191);
	    User::observe( UserObserver::class );
		Account::observe( AccountObserver::class );
		AccountEntry::observe( AccountEntryObserver::class);
    }
}
