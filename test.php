<?php

// All sample code is provided for illustrative purposes only.
// These examples have not been thoroughly tested under all conditions.
// The creator cannot guarantee or imply reliability, serviceability, or function of these programs.
// All programs contained herein are provided to you “AS IS” without any warranties of any kind.

// Give input from command line or url to choose function 1-22.
// $: php test.php function=<number of the function>
// http(s)://<base_url>/test.php?function=<number of the function>

// Please be advised that functions for global data require a pro+ subscription.

header('Content-type: application/json');

// Get parameters from command line or url.
if (isset($argv[1])) {
  $function = intval(explode('=', $argv[1])[1]);
} elseif (isset($_GET["function"])) {
  $function = isset($_GET["function"]) ? intval($_GET["function"]) : null;
} else {
  echo "Please enter a function number. for example via cli: 'php test.php function=1', or add for example '?function=1' to the url.\n";
  die();
}

// Import the API class file.
require_once 'BorsdataAPI.php';

// Initiate the API class.
$borsdata = new BorsdataAPI();

// Test parameters for functions.
$instruments = "instruments"; // Options: branches, countries, markets, sectors, instruments, translationmetadata
$insId = "2"; // Get all different id's with the get_all_instruments('instruments') function.
$kpiId = "1"; // Get all different id's with the get_kpi_metadata() function.
$reportType = "year"; // Options: year, quarter, r12
$priceType = "mean"; // Options: low, mean, high
$calcGroup = "last"; // For KPI-Screener, for more info about the parameter, see link below.
$calc = "latest"; // For KPI-Screener, for more info about the parameter, see link below.
$from = "2022-01-01"; // For stock price history. (optional, can be empty)
$to = "2022-12-31"; // For stock price history. (optional, can be empty)
$maxCount = "10"; // 10 default. year=20 max, r12 & quarter=40 max. (optional, can be empty)
$maxYearCount = "2"; // 10 default, 20 max.
$maxR12QCount = "2"; // 10 default, 40 max.
$date = "2023-08-15"; // For stockprices date.
$instList = "2,3,6"; // List of instrument id's.

// Examples for getting data from the different functions and print out the resulting array.
// All parameters in the function is required for the function to work properly.
// If any optional parameter should be empty, set that parameter to an empty value, e.g. $from = ""

$result = match ($function) {

  // Instruments: https://github.com/Borsdata-Sweden/API/wiki/Instruments
  1 => $borsdata->getAllInstruments($instruments),
  2 => $borsdata->getAllGlobalInstruments($instruments),
  3 => $borsdata->getAllUpdatedInstruments(),

  // KPI History: https://github.com/Borsdata-Sweden/API/wiki/KPI-History
  4 => $borsdata->getKpiHistory($insId, $kpiId, $reportType, $priceType, $maxCount),
  5 => $borsdata->getKpiSummary($insId, $reportType, $maxCount),
  6 => $borsdata->getHistoricalKpis($kpiId, $reportType, $priceType, $instList),

  // KPI Screener: https://github.com/Borsdata-Sweden/API/wiki/KPI-Screener
  7 => $borsdata->getKpidataForOneInstrument($insId, $kpiId, $calcGroup, $calc),
  8 => $borsdata->getKpidataForAllInstruments($kpiId, $calcGroup, $calc),
  9 => $borsdata->getKpidataForAllGlobalInstruments($kpiId, $calcGroup, $calc),
  10 => $borsdata->getKpiUpdated(),
  11 => $borsdata->getKpiMetadata(),

  // Reports: https://github.com/Borsdata-Sweden/API/wiki/Reports
  12 => $borsdata->getReportsByType($insId, $reportType, $maxCount),
  13 => $borsdata->getReportsForAllTypes($insId, $maxYearCount, $maxR12QCount),
  14 => $borsdata->getAllReports($instList, $maxYearCount, $maxR12QCount),
  15 => $borsdata->getReportsMetadata(),

  // Stock price: https://github.com/Borsdata-Sweden/API/wiki/Stockprice
  16 => $borsdata->getStockpricesForInstrument($insId, $from, $to, $maxCount),
  17 => $borsdata->getLastStockprices(),
  18 => $borsdata->getLastGlobalStockprices(),
  19 => $borsdata->getStockpricesForDate($date),
  20 => $borsdata->getGlobalStockpricesForDate($date),
  21 => $borsdata->getHistoricalStockprices($instList, $from, $to),

  // Stock splits: Max 1 year history.
  22 => $borsdata->getStocksplits(),

  default => "No function selected\n",
};

print_r($result);
