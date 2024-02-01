<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

	public const TABLE_NAME = 'users';
	public const ID = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function resolveRouteBinding($value, $field = null): ?Model
    {
        return $this->where('username', $value)->orWhere('id', $value)->firstOrFail();
    }

	public function accounts(): HasMany
	{
		return $this->hasMany(Account::class);
	}

	public function totalAmount(): float
	{
		return $this->accounts()->sum(Account::BALANCE);
	}

	public function totalCredit(): float
	{
		$total = 0.0;
		$accounts = $this->accounts;

		foreach ($accounts as $account) {
			$total += $account->totalCredit();
		}

		return $total;
	}

	public function totalDebit(): float
	{
		$total = 0.0;
		$accounts = $this->accounts;

		foreach ($accounts as $account) {
			$total += $account->totalDebit();
		}

		return $total;
	}

	public function accountEntries($accountId = null)
	{
		$accountIds = $accountId ? [$accountId] : $this->accounts->pluck(Account::ID)->toArray();

		return AccountEntry::whereIn(AccountEntry::ACCOUNT_ID, $accountIds)->latest(AccountEntry::DATE)->paginate(25);
	}
}
