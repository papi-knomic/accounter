<?php

namespace App\Http\Requests;

use App\Models\Account;
use App\Models\AccountEntry;
use App\Models\Category;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateAccountEntryRequest extends FormRequest
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
	 * @return array
	 */
    public function rules(): array
    {
        return [
	        AccountEntry::DESCRIPTION => 'required|string',
	        AccountEntry::AMOUNT => [
		        'required',
		        'numeric',
		        'min:1',
	        ],
	        AccountEntry::TYPE => [
		        'required',
		        Rule::in(AccountEntry::TYPES),
	        ],
	        AccountEntry::ACCOUNT_ID => 'required|exists:' . Account::TABLE_NAME . ',' . Account::ID,
	        AccountEntry::DATE => 'nullable|date',
	        AccountEntry::CATEGORY_ID => 'exists:' . Category::TABLE_NAME . ',' . Category::ID
        ];
    }
}
