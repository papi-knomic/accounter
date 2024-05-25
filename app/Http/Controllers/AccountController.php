<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAccountRequest;
use App\Http\Requests\UpdateAccountRequest;
use App\Http\Resources\AccountResource;
use App\Models\Account;
use App\Models\AccountEntry;
use App\Models\User;
use App\Repositories\AccountRepository;
use App\Services\CustomResponse;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AccountController extends Controller
{
	/**
	 * @var AccountRepository
	 */
	private AccountRepository $accountRepository;

	public function __construct(AccountRepository $accountRepository)
	{
		$this->accountRepository = $accountRepository;
	}

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
		$keyword = $request->input('keyword') ?? '';
		$categories = $request->input('categories', '');
		$categories = empty($categories) ? [] : explode(',', $categories);

		if ($date && !isValidDate($date)) {
			return CustomResponse::errorResponse('Please pass valid date', 401);
		}

		if (!$date) {
			$date = Carbon::today()->toDateString();
		}

		if ($account_id && !accountBelongsToUser($account_id)) {
			return CustomResponse::errorResponse('Account id is not valid', 403);
		}

		$account_ids = $account_id ? [$account_id] : getUserAccountsID();

		// Check if the date is a range
		if (str_contains($date, ',')) {
			$dates = explode(',', $date);
			$startDate = trim($dates[0]);
			$endDate = trim($dates[1]);

			$data = $this->accountRepository->getRangeSummary($startDate, $endDate, $account_ids, $keyword, $categories);
		} else {
			$data = $this->accountRepository->getDailySummary($date, $account_ids, $keyword, $categories);
		}

		return CustomResponse::successResponseWithData($data);
	}


	/**
	 * Get summary
	 */
	public function getDetailed(Request $request): JsonResponse
	{
		$date = $request->input('date');
		$account_id = $request->input('account_id');
		$keyword = $request->input('keyword') ?? '';
		$categories = $request->input('categories', '');
		$categories = empty($categories) ? [] : explode(',', $categories);

		if ($date && !isValidDate($date)) {
			return CustomResponse::errorResponse('Please pass valid date', 401);
		}

		if (!$date) {
			$date = Carbon::today()->toDateString();
		}

		if ($account_id && !accountBelongsToUser($account_id)) {
			return CustomResponse::errorResponse('Account id is not valid', 403);
		}

		$account_ids = $account_id ? [$account_id] : getUserAccountsID();

		// Check if the date is a range
		if (str_contains($date, ',')) {
			$dates = explode(',', $date);
			$startDate = trim($dates[0]);
			$endDate = trim($dates[1]);

			$data = $this->accountRepository->getRangeDetailed($startDate, $endDate, $account_ids, $keyword, $categories);
		} else {
			$data[$date] = $this->accountRepository->getDailySummary($date, $account_ids, $keyword, $categories);
		}

		return CustomResponse::successResponseWithData($data);
	}
}
