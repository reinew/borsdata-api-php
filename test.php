<?php

// All sample code is provided for illustrative purposes only.
// These examples have not been thoroughly tested under all conditions.
// The creator cannot guarantee or imply reliability, serviceability, or function of these programs.
// All programs contained herein are provided to you “AS IS” without any warranties of any kind.

// Give input from command line or url to choose function 1-18.
// $: php test.php function=<number of the function>
// http(s)://<base_url>/test.php?function=<number of the function>

header('Content-type: application/json');

// Get parameters from command line or url.
if (isset($argv)) {
  parse_str(implode('&', array_slice($argv, 1)), $_GET);
  $function = $_GET["function"];
} else {
  $function = $_GET["function"];
}

// Import the API class file.
require_once 'BorsdataAPI.php';

// Initiate the API class.
$borsdata = new BorsdataAPI();

// Test parameters for functions.
$instrument = "instruments"; // Options: branches, countries, markets, sectors, instruments, translationmetadata
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
// If any optional parameter should be empty, set that parameter to ""

// Instruments: https://github.com/Borsdata-Sweden/API/wiki/Instruments
if ($function == 1) {
  print_r($borsdata->get_all_instruments($instrument));
}
if ($function == 2) {
  print_r($borsdata->get_all_updated_instruments());
}

// KPI History: https://github.com/Borsdata-Sweden/API/wiki/KPI-History
if ($function == 3) {
  print_r($borsdata->get_kpi_history($insId, $kpiId, $reportType, $priceType, $maxCount));
}
if ($function == 4) {
  print_r($borsdata->get_kpi_summary($insId, $reportType, $maxCount));
}
if ($function == 5) {
  print_r($borsdata->get_historical_kpis($kpiId, $reportType, $priceType, $instList));
}

// KPI Screener: https://github.com/Borsdata-Sweden/API/wiki/KPI-Screener
if ($function == 6) {
  print_r($borsdata->get_kpidata_for_one_instrument($insId, $kpiId, $calcGroup, $calc));
}
if ($function == 7) {
  print_r($borsdata->get_kpidata_for_all_instruments($kpiId, $calcGroup, $calc));
}
if ($function == 8) {
  print_r($borsdata->get_kpi_updated());
}
if ($function == 9) {
  print_r($borsdata->get_kpi_metadata());
}

// Reports: https://github.com/Borsdata-Sweden/API/wiki/Reports
if ($function == 10) {
  print_r($borsdata->get_reports_by_type($insId, $reportType, $maxCount));
}
if ($function == 11) {
  print_r($borsdata->get_reports_for_all_types($insId, $maxYearCount, $maxR12QCount));
}
if ($function == 12) {
  print_r($borsdata->get_all_reports($instList, $maxYearCount, $maxR12QCount));
}
if ($function == 13) {
  print_r($borsdata->get_reports_metadata());
}

// Stock price: https://github.com/Borsdata-Sweden/API/wiki/Stockprice
if ($function == 14) {
  print_r($borsdata->get_stockprices_for_instrument($insId, $from, $to, $maxCount));
}
if ($function == 15) {
  print_r($borsdata->get_last_stockprices());
}
if ($function == 16) {
  print_r($borsdata->get_stockprices_for_date($date));
}
if ($function == 17) {
  print_r($borsdata->get_historical_stockprices($instList, $from, $to));
}

// Stock splits: Max 1 year history.
if ($function == 18) {
  print_r($borsdata->get_stocksplits());
}
