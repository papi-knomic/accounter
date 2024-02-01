<?php

namespace App\Http\Requests;

use App\Models\Account;
use App\Models\AccountEntry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAccountEntryRequest extends FormRequest
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
		    AccountEntry::DESCRIPTION => 'string',
		    AccountEntry::AMOUNT => [
			    'numeric',
			    'min:1',
		    ],
		    AccountEntry::TYPE => [
			    Rule::in(AccountEntry::TYPES),
		    ],
		    AccountEntry::DATE => 'nullable|date'
	    ];
    }
}
