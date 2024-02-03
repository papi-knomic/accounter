<?php

namespace App\Repositories;

use App\Interfaces\AccountRepositoryInterface;
use App\Models\Account;
use App\Models\AccountEntry;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class AccountRepository implements AccountRepositoryInterface
{

	public function getDailySummary(string $date, array $accountIDs): array
	{
		$entries = AccountEntry::whereIn(AccountEntry::ACCOUNT_ID, $accountIDs)
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

		return [
			'date' => $date,
			Account::TRANSACTION_COUNT => $totalCount,
			'credit_count' => $creditCount,
			'debit_count' => $debitCount,
			'total_credit' => number_format($creditSum, 2),
			'total_debit' => number_format($debitSum, 2),
		];
	}

	public function getRangeSummary(string $startDate, string $endDate, array $accountIDs): array
	{
		$dateRange = CarbonPeriod::create($startDate, $endDate);
		$daysCount = $dateRange->count();

		$startDate = Carbon::parse($startDate)->startOfDay();
		$endDate = Carbon::parse($endDate)->endOfDay();

		$entries = AccountEntry::whereIn(AccountEntry::ACCOUNT_ID, $accountIDs)
			->whereBetween(AccountEntry::DATE, [$startDate, $endDate])
			->selectRaw('SUM(CASE WHEN type = "debit" THEN amount ELSE 0 END) as debitSum')
			->selectRaw('SUM(CASE WHEN type = "credit" THEN amount ELSE 0 END) as creditSum')
			->selectRaw('COUNT(CASE WHEN type = "debit" THEN 1 END) as debitCount')
			->selectRaw('COUNT(CASE WHEN type = "credit" THEN 1 END) as creditCount')
			->selectRaw('COUNT(*) as totalCount')
			->get();

		$debitSum = $entries->sum('debitSum');
		$creditSum = $entries->sum('creditSum');
		$debitCount = $entries->sum('debitCount');
		$creditCount = $entries->sum('creditCount');
		$totalCount = $entries->sum('totalCount');

		// Calculate daily averages
		$dailyAverageDebit = ($daysCount > 0) ? ($debitSum / $daysCount) : 0;
		$dailyAverageCredit = ($daysCount > 0) ? ($creditSum / $daysCount) : 0;
		$dailyAverageTransaction = ($daysCount > 0) ? ($totalCount / $daysCount) : 0;

		return [
			'start_date' => $startDate->toDateString(),
			'end_date' => $endDate->toDateString(),
			Account::TRANSACTION_COUNT => $totalCount,
			'credit_count' => $creditCount,
			'debit_count' => $debitCount,
			'total_credit' => number_format($creditSum, 2),
			'total_debit' => number_format($debitSum, 2),
			'daily_average_debit' => number_format($dailyAverageDebit, 2),
			'daily_average_credit' => number_format($dailyAverageCredit, 2),
			'daily_average_transaction' => number_format($dailyAverageTransaction, 2),
		];
	}
}