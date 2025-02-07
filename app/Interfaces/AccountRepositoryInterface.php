<?php

namespace App\Interfaces;

interface AccountRepositoryInterface
{
	public function getDailySummary( string $date, array $accountIDs, string $keyword, array $categories ) : array;
	public function getRangeSummary( string $startDate, string $endDate, array $accountIDs, string $keyword, array $categories ) : array;
	public function getRangeDetailed( string $startDate, string $endDate, array $accountIDs, string $keyword, array $categories ) : array;
	public function getDetailedByCategory( string $startDate, string $endDate, array $accountIDs, string $keyword, array $categories ) : array;
}