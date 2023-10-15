<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAccountRequest;
use App\Models\Account;
use App\Models\User;
use App\Services\CustomResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(CreateAccountRequest $request) : JsonResponse
    {
		$fields = $request->validated();
		$fields[Account::USER_ID] = auth()->id();
		$accountsCount = auth()->user()->accounts()->count();

		if ($accountsCount >= 5) {
			return CustomResponse::errorResponse('You can not have more than 5 accounts');
		}

		$account = Account::create($fields);

		return CustomResponse::successResponseWithData($account);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Account $account)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Account $account)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Account $account)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Account $account)
    {
        //
    }
}
