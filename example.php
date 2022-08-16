<?php

// All sample code is provided for illustrative purposes only.
// These examples have not been thoroughly tested under all conditions.
// The creator cannot guarantee or imply reliability, serviceability, or function of these programs.
// All programs contained herein are provided to you “AS IS” without any warranties of any kind.

header('Content-type: application/json');

// This simple example imports the API class file, intiates the class, \
// returns an array of all instruments and prints the result in json format.

// Import the API class file.
require_once 'Borsdata.php';

// Initiate functions for Borsdata API.
$borsdata = new Borsdata();

// Set the api key.
$borsdata->set_apikey('');

// Make the API call.
$data = $borsdata->get_all_instruments("instruments");

// Print in json format for all instruments.
echo json_encode($data, JSON_PRETTY_PRINT);

?>
