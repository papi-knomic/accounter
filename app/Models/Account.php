<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

	protected $guarded = ['id', 'uuid'];

	public const TABLE_NAME = 'accounts';
	public const ID = 'id';
	public const UUID = 'uuid';
	public const ACCOUNT_NAME = 'account_name';
	public const BALANCE = 'balance';
	public const TRANSACTION_COUNT = 'transaction_count';
	public const USER_ID = 'user_id';
}
