<?php

namespace App\Http\Requests;

use App\Models\Account;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateAccountRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
	        Account::ACCOUNT_NAME => [
		        'required',
		        Rule::unique(Account::TABLE_NAME, Account::ACCOUNT_NAME)->where(function ($query) {
			        return $query->where(Account::USER_ID, $this->user()->id); // Assuming user ID is stored in the 'user_id' field in the 'accounts' table
		        }),
	        ],
	        Account::BALANCE => [
		        'required',
		        'numeric',
		        'min:0',
		        'max:100000000', // This sets the maximum balance to 100 million
	        ],
        ];
    }
}
