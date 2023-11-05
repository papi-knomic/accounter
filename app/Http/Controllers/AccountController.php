<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAccountRequest;
use App\Http\Requests\UpdateAccountRequest;
use App\Http\Resources\AccountResource;
use App\Models\Account;
use App\Models\AccountEntry;
use App\Models\User;
use App\Services\CustomResponse;
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
    public function getAll()
    {
        $accounts = auth()->user()->accounts;

		$accounts = AccountResource::collection($accounts);

	    return CustomResponse::successResponseWithData($accounts);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAccountRequest $request, Account $account)
    {
		$fields = $request->validated();
		if (!empty($fields[Account::BALANCE])) {
			if (AccountEntry::where('account_id', $account->id)->exists()) {
				return CustomResponse::errorResponse('');
			}
		}
        $account::update($fields);

	    return CustomResponse::successResponseWithData($account);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Account $account)
    {
        $account->delete();

	    return CustomResponse::successResponse('Account deleted successfully');
    }
}
