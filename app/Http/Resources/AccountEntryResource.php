<?php

namespace App\Http\Resources;

use App\Models\AccountEntry;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountEntryResource extends JsonResource
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
			AccountEntry::ID => $request[AccountEntry::ID],
	        AccountEntry::UUID => $request[AccountEntry::UUID],
	        AccountEntry::DESCRIPTION => $request[AccountEntry::DESCRIPTION],
	        AccountEntry::AMOUNT => number_format($request[AccountEntry::AMOUNT], 2),
	        AccountEntry::TYPE => $request[AccountEntry::TYPE],
	        AccountEntry::ACCOUNT_ID => $request[AccountEntry::ACCOUNT_ID],
	        AccountEntry::DATE => $request[AccountEntry::DATE],
	        AccountEntry::CATEGORY => $this->category->name
        ];
    }
}
