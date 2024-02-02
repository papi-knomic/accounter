<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAccountEntryRequest;
use App\Http\Requests\UpdateAccountEntryRequest;
use App\Http\Resources\AccountEntryResource;
use App\Models\Account;
use App\Models\AccountEntry;
use App\Services\AccountEntryService;
use App\Services\CustomResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AccountEntryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
	    $account_id = $request->input('account_id');

	    if ($account_id && !accountBelongsToUser($account_id)) {
		    return CustomResponse::errorResponse('Unauthorized', 403);
	    }

	    $entries = auth()->user()->accountEntries($account_id);
		$entries = AccountEntryResource::collection($entries)->response()->getData(true);

		return CustomResponse::successResponseWithData($entries);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateAccountEntryRequest $request): JsonResponse
    {
		$fields = $request->validated();

		$type = $fields[AccountEntry::TYPE];
		$accountID = $fields[AccountEntry::ACCOUNT_ID];
	    $amount = $fields[AccountEntry::AMOUNT];

		if ( AccountEntry::DEBIT == $type ) {
			$account = Account::find($accountID);

			if ($amount > $account->balance) {
				return CustomResponse::errorResponse('Debit balance is greater than account balance', Response::HTTP_BAD_REQUEST);
			}
		}

	    $data = [
		    AccountEntry::DESCRIPTION => $fields[AccountEntry::DESCRIPTION],
		    AccountEntry::TYPE => $type,
		    AccountEntry::AMOUNT => $fields[AccountEntry::AMOUNT],
		    AccountEntry::ACCOUNT_ID => $accountID,
		    AccountEntry::DATE => $fields[AccountEntry::DATE] ?? ''
	    ];
		$accountEntry = AccountEntryService::create($data);
		$entry = new AccountEntryResource($accountEntry);

		return CustomResponse::successResponseWithData($entry);
    }

    /**
     * Display the specified resource.
     */
    public function show(AccountEntry $accountEntry): JsonResponse
    {
	    $entry = new AccountEntryResource($accountEntry);

	    return CustomResponse::successResponseWithData($entry);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAccountEntryRequest $request, AccountEntry $accountEntry): JsonResponse
    {
	    // Validate and update the account entry
	   $fields = $request->validated();

	   $accountEntry->update($fields);
	   return CustomResponse::successResponse('Account entry updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AccountEntry $accountEntry): JsonResponse
    {
		$accountEntry->delete();

	    return CustomResponse::successResponse('Account entry deleted successfully');
    }
}
