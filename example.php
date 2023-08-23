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
 * This simple example imports the API class file, initiates the class,
 * returns an object of all instruments and prints the result in json format.
 *
 * Run this script with the following command: php example.js
 */

header('Content-type: application/json');

// Import the API class file.
require_once 'BorsdataAPI.php';

// Initiate functions from Borsdata API class.
$borsdata = new BorsdataAPI();

// Make the API call.
$data = $borsdata->getAllInstruments('instruments');

// Print in json format for all instruments.
echo json_encode($data, JSON_PRETTY_PRINT);
