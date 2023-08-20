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
$from = "2019-01-01"; // For stock price history. (optional, can be empty)
$to = "2019-12-31"; // For stock price history. (optional, can be empty)
$maxCount = "10"; // 10 default. year=20 max, r12 & quarter=40 max. (optional, can be empty)
$maxYearCount = "10"; // 10 default, 20 max.
$maxR12QCount = "10"; // 10 default, 40 max.
$date = "2022-08-15"; // For stockprices date.
$instList = "2,3,5"; // List of instrument id's.

// Examples for getting data from the different functions and print out the resulting array.
// All parameters in the function is required for the function to work properly.
// If any optional parameter should be empty, set that parameter to an empty value, e.g. $from = ""

$result = match ($function) {

  // Instruments: https://github.com/Borsdata-Sweden/API/wiki/Instruments
  1 => $borsdata->get_all_instruments($instruments),
  2 => $borsdata->get_all_global_instruments($instruments),
  3 => $borsdata->get_all_updated_instruments(),

  // KPI History: https://github.com/Borsdata-Sweden/API/wiki/KPI-History
  4 => $borsdata->get_kpi_history($insId, $kpiId, $reportType, $priceType, $maxCount),
  5 => $borsdata->get_kpi_summary($insId, $reportType, $maxCount),
  6 => $borsdata->get_historical_kpis($kpiId, $reportType, $priceType, $instList),

  // KPI Screener: https://github.com/Borsdata-Sweden/API/wiki/KPI-Screener
  7 => $borsdata->get_kpidata_for_one_instrument($insId, $kpiId, $calcGroup, $calc),
  8 => $borsdata->get_kpidata_for_all_instruments($kpiId, $calcGroup, $calc),
  9 => $borsdata->get_kpidata_for_all_global_instruments($kpiId, $calcGroup, $calc),
  10 => $borsdata->get_kpi_updated(),
  11 => $borsdata->get_kpi_metadata(),

  // Reports: https://github.com/Borsdata-Sweden/API/wiki/Reports
  12 => $borsdata->get_reports_by_type($insId, $reportType, $maxCount),
  13 => $borsdata->get_reports_for_all_types($insId, $maxYearCount, $maxR12QCount),
  14 => $borsdata->get_all_reports($instList, $maxYearCount, $maxR12QCount),
  15 => $borsdata->get_reports_metadata(),

  // Stock price: https://github.com/Borsdata-Sweden/API/wiki/Stockprice
  16 => $borsdata->get_stockprices_for_instrument($insId, $from, $to, $maxCount),
  17 => $borsdata->get_last_stockprices(),
  18 => $borsdata->get_last_global_stockprices(),
  19 => $borsdata->get_stockprices_for_date($date),
  20 => $borsdata->get_global_stockprices_for_date($date),
  21 => $borsdata->get_historical_stockprices($instList, $from, $to),

  // Stock splits: Max 1 year history.
  22 => $borsdata->get_stocksplits(),

  default => "No function selected\n",
};

print_r($result);
