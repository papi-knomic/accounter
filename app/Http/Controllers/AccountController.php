<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAccountRequest;
use App\Http\Requests\UpdateAccountRequest;
use App\Http\Resources\AccountResource;
use App\Models\Account;
use App\Models\AccountEntry;
use App\Models\User;
use App\Services\CustomResponse;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AccountController extends Controller
{

    /**
     * Show the form for creating a new resource.
     */
    public function store(CreateAccountRequest $request) : JsonResponse
    {
		$fields = $request->validated();
		$fields[Account::USER_ID] = auth()->id();
		$fields[Account::BALANCE] = 0;
		$fields[Account::TRANSACTION_COUNT] = 0;
		$accountsCount = auth()->user()->accounts()->count();

		if ($accountsCount >= 5) {
			return CustomResponse::errorResponse('You can not have more than 5 accounts');
		}

		$account = Account::create($fields);
		$account = new AccountResource($account);

		return CustomResponse::successResponseWithData($account, 'Account has been created', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Account $account)
    {
	    $account = new AccountResource($account);

	    return CustomResponse::successResponseWithData($account,);
    }

    /**
     * Get User Accounts
     */
    public function getAll(): JsonResponse
    {
        $accounts = auth()->user()->accounts;

		$accounts = AccountResource::collection($accounts)->response()->getData(true);

	    return CustomResponse::successResponseWithData($accounts);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAccountRequest $request, Account $account): JsonResponse
    {
		$fields = $request->validated();
		if (!empty($fields[Account::BALANCE])) {
			if (AccountEntry::where('account_id', $account->id)->exists()) {
				return CustomResponse::errorResponse('This account already has an entry you can not edit');
			}
		}
       $account->update($fields);

	    return CustomResponse::successResponseWithData($account);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Account $account): JsonResponse
    {
        $account->delete();

	    return CustomResponse::successResponse('Account deleted successfully');
    }


	/**
	 * Get summary
	 */
	public function getSummary(Request $request): JsonResponse
	{
		$date = $request->input('date');
		$account_id = $request->input('account_id');

		if ($date && !strtotime($date)) {
			return CustomResponse::errorResponse('Please pass valid date', 401);
		}

		if (!$date) {
			$date = Carbon::today()->toDateString();
		}

		if ($account_id && !accountBelongsToUser($account_id)) {
			return CustomResponse::errorResponse('Account id is not valid', 403);
		}

		$account_ids = $account_id ? [$account_id] : auth()->user()->accounts->pluck(Account::ID)->toArray();

		$entries = AccountEntry::whereIn(AccountEntry::ACCOUNT_ID, $account_ids)
			->whereDate(AccountEntry::DATE, '=', Carbon::parse($date)->toDateString())
			->selectRaw('SUM(CASE WHEN type = "debit" THEN amount ELSE 0 END) as debitSum')
			->selectRaw('SUM(CASE WHEN type = "credit" THEN amount ELSE 0 END) as creditSum')
			->selectRaw('COUNT(CASE WHEN type = "debit" THEN 1 END) as debitCount')
			->selectRaw('COUNT(CASE WHEN type = "credit" THEN 1 END) as creditCount')
			->selectRaw('COUNT(*) as totalCount')
			->first();

		$debitSum = $entries->debitSum;
		$creditSum = $entries->creditSum;
		$debitCount = $entries->debitCount;
		$creditCount = $entries->creditCount;
		$totalCount = $entries->totalCount;

		$data = [
			'date' => $date,
			Account::TRANSACTION_COUNT => $totalCount,
			'credit_count' => $creditCount,
			'debit_count' => $debitCount,
			'total_credit' => number_format($creditSum, 2),
			'total_debit' => number_format($debitSum, 2),
		];

		return CustomResponse::successResponseWithData($data);
	}
}
