<?php

namespace App\Interfaces;

interface AccountRepositoryInterface
{
	public function getDailySummary( string $date, array $accountIDs ) : array;

	public function getRangeSummary( string $startDate, string $endDate, array $accountIDs ) : array;
}