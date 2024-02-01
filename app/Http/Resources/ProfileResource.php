<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request) : array
    {
        return [
            "id" => $this->id,
            "first_name" => $this->first_name,
            "last_name" => $this->last_name,
            "email" => $this->email,
            "username" => $this->username,
            "email_verified" => (bool)$this->email_verified_at,
	        "accounts" =>  AccountResource::collection($this->accounts),
	        "total_accounts" => $this->accounts->count(),
	        "total_balance" => number_format($this->totalAmount(), 2),
	        "total_credit" => $this->totalCredit(),
	        "total_debit" => $this->totalDebit()
        ];
    }

}
