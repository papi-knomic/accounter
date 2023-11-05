<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    use UUID, HasFactory;

	protected $guarded = ['id', 'uuid'];

	public const TABLE_NAME = 'accounts';
	public const ID = 'id';
	public const UUID = 'uuid';
	public const ACCOUNT_NAME = 'account_name';
	public const BALANCE = 'balance';
	public const TRANSACTION_COUNT = 'transaction_count';
	public const USER_ID = 'user_id';

	public function entries() : HasMany
	{
		return $this->hasMany(AccountEntry::class);
	}

	public function resolveRouteBinding($value, $field = null): ?Model
	{
		return $this->where(self::UUID, $value)->orWhere(self::ID, $value)->firstOrFail();
	}

	public function totalCredit(): float
	{
		return $this->entries()->where(AccountEntry::TYPE, AccountEntry::CREDIT)->sum(AccountEntry::AMOUNT);
	}

	public function totalDebit(): float
	{
		return $this->entries()->where(AccountEntry::TYPE, AccountEntry::DEBIT)->sum(AccountEntry::AMOUNT);
	}
}
