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
		$keyword = $request->input('keyword') ?? '';
		$date = $request->input('date');

	    if ($account_id && !accountBelongsToUser($account_id)) {
		    return CustomResponse::errorResponse('Unauthorized', 403);
	    }

		$startDate = '';
		$endDate = '';

		if ($date) {
			if (!isValidDate($date)) {
				return CustomResponse::errorResponse('Please pass valid date', 401);
			}

			if (str_contains($date, ',')) {
				$dates = explode(',', $date);
				$startDate = trim($dates[0]);
				$endDate = trim($dates[1]);
			} else {
				$startDate = $date;
			}
		}

	    $account_ids = $account_id ? [$account_id] : getUserAccountsID();

	    $entries = auth()->user()->accountEntries($account_ids, $keyword, $startDate, $endDate);
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
		$categoryID = $fields[AccountEntry::CATEGORY_ID] ?? '';

		if ( AccountEntry::DEBIT == $type ) {
			$account = Account::find($accountID);

			if ($amount > $account->balance) {
				return CustomResponse::errorResponse('Debit balance is greater than account balance', Response::HTTP_BAD_REQUEST);
			}
		}

		if (empty($categoryID)) {
			if (AccountEntry::CREDIT == $type) {
				$categoryID = 2;
			} else {
				$categoryID = 1;
			}
		}

	    $data = [
		    AccountEntry::DESCRIPTION => $fields[AccountEntry::DESCRIPTION],
		    AccountEntry::TYPE => $type,
		    AccountEntry::AMOUNT => $fields[AccountEntry::AMOUNT],
		    AccountEntry::ACCOUNT_ID => $accountID,
		    AccountEntry::DATE => $fields[AccountEntry::DATE] ?? '',
		    AccountEntry::CATEGORY_ID => $categoryID
	    ];
		$accountEntry = AccountEntryService::create($data);
		$entry = new AccountEntryResource($accountEntry);

		$balance = number_format($accountEntry->account->balance, 2);
		$entry[Account::BALANCE] = $balance;

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
