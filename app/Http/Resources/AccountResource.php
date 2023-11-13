<?php

namespace App\Http\Resources;

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
	    $request = parent::toArray($request);

        return [
			Account::ID => $request[Account::ID],
			Account::UUID => $request[Account::UUID],
	        Account::ACCOUNT_NAME => $request[Account::ACCOUNT_NAME],
	        Account::BALANCE => $request[Account::BALANCE],
	        Account::TRANSACTION_COUNT => $request[Account::TRANSACTION_COUNT] ?? 0,
	        'total_credit' => $this->totalCredit(),
	        'total_debit' => $this->totalDebit(),
        ];
    }
}