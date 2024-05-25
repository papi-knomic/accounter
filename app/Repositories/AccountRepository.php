<?php

namespace App\Repositories;

use App\Interfaces\AccountRepositoryInterface;
use App\Models\Account;
use App\Models\AccountEntry;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class AccountRepository implements AccountRepositoryInterface
{

	public function getDailySummary(string $date, array $accountIDs, string $keyword, array $categories): array
	{
		$entries = AccountEntry::whereIn(AccountEntry::ACCOUNT_ID, $accountIDs)
			->where(AccountEntry::DESCRIPTION, 'LIKE', "%$keyword%")
			->when(!empty($categories), function ($query) use($categories) {
				$query->whereIn(AccountEntry::CATEGORY_ID, $categories);
			})
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

	public function getRangeSummary(string $startDate, string $endDate, array $accountIDs, string $keyword, array $categories): array
	{
		$dateRange = CarbonPeriod::create($startDate, $endDate);
		$daysCount = $dateRange->count();

		$startDate = Carbon::parse($startDate)->startOfDay();
		$endDate = Carbon::parse($endDate)->endOfDay();

		$entries = AccountEntry::whereIn(AccountEntry::ACCOUNT_ID, $accountIDs)
			->where(AccountEntry::DESCRIPTION, 'LIKE', "%$keyword%")
			->whereBetween(AccountEntry::DATE, [$startDate, $endDate])
			->when(!empty($categories), function ($query) use($categories) {
				$query->whereIn(AccountEntry::CATEGORY_ID, $categories);
			})
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
		$dailyAverageDebitCount = ($daysCount > 0) ? ($debitCount / $daysCount) : 0;
		$dailyAverageCreditCount = ($daysCount > 0) ? ($creditCount / $daysCount) : 0;
		$dailyAverageTransaction = ($daysCount > 0) ? ($totalCount / $daysCount) : 0;
		$balance = $creditSum - $debitSum;

		return [
			'start_date' => $startDate->toDateString(),
			'end_date' => $endDate->toDateString(),
			Account::TRANSACTION_COUNT => $totalCount,
			'credit_count' => $creditCount,
			'debit_count' => $debitCount,
			'total_credit' => number_format($creditSum, 2),
			'total_debit' => number_format($debitSum, 2),
			'balance' => number_format($balance, 2),
			'daily_average_debit' => number_format($dailyAverageDebit, 2),
			'daily_average_credit' => number_format($dailyAverageCredit, 2),
			'daily_average_debit_count' => number_format($dailyAverageDebitCount, 2),
			'daily_average_credit_count' => number_format($dailyAverageCreditCount, 2),
			'daily_average_transaction' => number_format($dailyAverageTransaction, 2),
		];
	}

	public function getRangeDetailed(string $startDate, string $endDate, array $accountIDs, string $keyword, array $categories): array
	{
		$data = [];
		$dateRange = CarbonPeriod::create($startDate, $endDate);
		foreach ($dateRange as $date) {
			$dateString = $date->toDateString();
			$result = $this->getDailySummary($dateString, $accountIDs, $keyword, $categories);
			if (0 == $result['credit_count'] && 0 == $result['debit_count']) {
				continue;
			}
			$data[$dateString] = $result;
		}

		return $data;
	}

	public function getDetailedByCategory(string $startDate, string $endDate, array $accountIDs, string $keyword, array $categories): array
	{
		// TODO: Implement getDetailedByCategory() method.
	}
}