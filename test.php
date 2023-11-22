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
$instrumentsOption = "instruments"; // Options: branches, countries, markets, sectors, instruments, translationmetadata
$holdingsOption = "insider"; // Options: insider, shorts, buyback
$calendarOption = "report"; // Options: report, dividend
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
$date = "2023-11-21"; // For stockprices date.
$instList = "2,3,6"; // List of instrument id's.

// Examples for getting data from the different functions and print out the resulting array.
// All parameters in the function is required for the function to work properly.
// If any optional parameter should be empty, set that parameter to an empty value, e.g. $from = ""

$result = match ($function) {

  // Instrument Meta: https://github.com/Borsdata-Sweden/API/wiki/Instruments
  1 => $borsdata->getAllInstruments($instrumentsOption),

  // Holdings: https://github.com/Borsdata-Sweden/API/wiki/Holdings
  2 => $borsdata->getHoldings($holdingsOption, $instList),

  // Instruments: https://github.com/Borsdata-Sweden/API/wiki/Instruments
  3 => $borsdata->getAllGlobalInstruments(),
  4 => $borsdata->getAllUpdatedInstruments(),
  5 => $borsdata->getInstrumentDescriptions($instList),

  // Calendar: https://github.com/Borsdata-Sweden/API/wiki/Calendar
  6 => $borsdata->getCalendar($calendarOption, $instList),

  // KPI History: https://github.com/Borsdata-Sweden/API/wiki/KPI-History
  7 => $borsdata->getKpiHistory($insId, $kpiId, $reportType, $priceType, $maxCount),
  8 => $borsdata->getKpiSummary($insId, $reportType, $maxCount),
  9 => $borsdata->getHistoricalKpis($kpiId, $reportType, $priceType, $instList),

  // KPI Screener: https://github.com/Borsdata-Sweden/API/wiki/KPI-Screener
  10 => $borsdata->getKpidataForOneInstrument($insId, $kpiId, $calcGroup, $calc),
  11 => $borsdata->getKpidataForAllInstruments($kpiId, $calcGroup, $calc),
  12 => $borsdata->getKpidataForAllGlobalInstruments($kpiId, $calcGroup, $calc),
  13 => $borsdata->getKpiUpdated(),
  14 => $borsdata->getKpiMetadata(),

  // Reports: https://github.com/Borsdata-Sweden/API/wiki/Reports
  15 => $borsdata->getReportsByType($insId, $reportType, $maxCount),
  16 => $borsdata->getReportsForAllTypes($insId, $maxYearCount, $maxR12QCount),
  17 => $borsdata->getReportsMetadata(),
  18 => $borsdata->getAllReports($instList, $maxYearCount, $maxR12QCount),

  // Stock price: https://github.com/Borsdata-Sweden/API/wiki/Stockprice
  19 => $borsdata->getStockpricesForInstrument($insId, $from, $to, $maxCount),
  20 => $borsdata->getStockPricesForListOfInstruments($instList, $from, $to),
  21 => $borsdata->getLastStockprices(),
  22 => $borsdata->getLastGlobalStockprices(),
  23 => $borsdata->getStockpricesForDate($date),
  24 => $borsdata->getGlobalStockpricesForDate($date),

  // Stock splits: Max 1 year history.
  25 => $borsdata->getStocksplits(),

  default => "No function selected\n",
};

// Print out the result in JSON format.
echo json_encode($result, JSON_PRETTY_PRINT);
