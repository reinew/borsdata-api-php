<?php

/**
 * @author ReineW
 * @license MIT
 * @link https://github.com/reinew/borsdata-api
 *
 * All sample code is provided for illustrative purposes only.
 * These examples have not been thoroughly tested under all conditions.
 * The creator cannot guarantee or imply reliability, serviceability, or function of this class.
 * All code contained herein are provided to you “AS IS” without any warranties of any kind.
 *
 * Give input from command line or url to choose function 1-22.
 * $: php test.php <number of the function>
 * http(s)://<base_url>/test.php?function=<number of the function>
 */

header('Content-type: application/json');

// Get parameters from command line or url.
if (isset($argv[1])) {
  $function = intval($argv[1]);
} elseif (isset($_GET["function"])) {
  $function = isset($_GET["function"]) ? intval($_GET["function"]) : null;
} else {
  echo "Please enter a function number between 1-22. for example via cli: 'php test.php 1', or add for example '?function=1' to the url.\n";
  die();
}

// Import the API class file.
require_once 'BorsdataAPI.php';

// Initiate the API class.
$borsdata = new BorsdataAPI();

// Test parameters for functions.
$option = "instruments"; // Options: branches, countries, markets, sectors, instruments, translationmetadata
$insId = "2"; // Get all different id's with the "getAllInstruments('instruments')" function.
$kpiId = "1"; // Get all different id's with the "getKpiMetadata()" function.
$reportType = "year"; // Options: year, quarter, r12
$priceType = "mean"; // Options: low, mean, high
$calcGroup = "last"; // For KPI-Screener, for more info about the parameter, see link below.
$calc = "latest"; // For KPI-Screener, for more info about the parameter, see link below.
$from = "2022-01-01"; // For stock price history. (optional)
$to = "2022-12-31"; // For stock price history. (optional)
$maxCount = "10"; // 10 default. year=20 max, r12 & quarter=40 max. (optional)
$maxYearCount = "2"; // 10 default, 20 max. (optional)
$maxR12QCount = "2"; // 10 default, 40 max. (optional)
$date = "2023-08-15"; // For stockprices date.
$instList = "2,3,6"; // List of instrument id's.

// Examples for getting data from the different functions and print out the resulting array.
// All parameters in the function is required for the function to work properly.
// If any optional parameter should be empty, set that parameter to an empty value, e.g. $from = ""

$result = match ($function) {

  // Instruments: https://github.com/Borsdata-Sweden/API/wiki/Instruments
  1 => $borsdata->getAllInstruments($option),
  2 => $borsdata->getAllGlobalInstruments(),
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
  14 => $borsdata->getReportsMetadata(),
  15 => $borsdata->getAllReports($instList, $maxYearCount, $maxR12QCount),

  // Stock price: https://github.com/Borsdata-Sweden/API/wiki/Stockprice
  16 => $borsdata->getStockpricesForInstrument($insId, $from, $to, $maxCount),
  17 => $borsdata->getStockPricesForListOfInstruments($instList, $from, $to),
  18 => $borsdata->getLastStockprices(),
  19 => $borsdata->getLastGlobalStockprices(),
  20 => $borsdata->getStockpricesForDate($date),
  21 => $borsdata->getGlobalStockpricesForDate($date),

  // Stock splits: Max 1 year history.
  22 => $borsdata->getStocksplits(),

  default => "No function selected\n",
};

// Print out the result in JSON format.
echo json_encode($result, JSON_PRETTY_PRINT);
