<?php

namespace App\Http\Controllers;

use App\Http\Resources\AccountEntryResource;
use App\Models\AccountEntry;
use App\Services\CustomResponse;
use Illuminate\Http\Request;

class AccountEntryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
		$entries = auth()->user()->accountEntries();
		$entries = AccountEntryResource::collection($entries);

		return CustomResponse::successResponseWithData($entries);
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
    public function show(AccountEntry $accountEntry)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AccountEntry $accountEntry)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AccountEntry $accountEntry)
    {
        //
    }
}
