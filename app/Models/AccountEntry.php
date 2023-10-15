<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountEntry extends Model
{
    use HasFactory, UUID;

	public const TABLE_NAME = 'account_entries';
	public const ID = 'id';
	public const UUID = 'uuid';
	public const DESCRIPTION = 'description';
	public const AMOUNT = 'amount';
	public const TYPE = 'type';
	public const CREDIT = 'credit';
	public const DEBIT = 'debit';
	public const TYPES = [ self::CREDIT, self::DEBIT];
	public const ACCOUNT_ID = 'account_id';


	protected $guarded = [ self::ID, self::UUID ];
}
