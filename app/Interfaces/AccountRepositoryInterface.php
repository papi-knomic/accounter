<?php

namespace App\Interfaces;

interface AccountRepositoryInterface
{
	public function getDailySummary( string $date, array $accountIDs, string $keyword ) : array;

	public function getRangeSummary( string $startDate, string $endDate, array $accountIDs, string $keyword ) : array;
}