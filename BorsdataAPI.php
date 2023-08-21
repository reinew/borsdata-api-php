<?php

// All sample code is provided for illustrative purposes only.
// These examples have not been thoroughly tested under all conditions.
// The creator cannot guarantee or imply reliability, serviceability, or function of these programs.
// All programs contained herein are provided to you “AS IS” without any warranties of any kind.

// Please be advised that functions for global instruments require a pro+ subscription.

class BorsdataAPI
{

  private $BASEURL = 'https://apiservice.borsdata.se';
  private $VERSION = 'v1';
  private $key;
  private $SLEEP = 0.11; // Max 100 requests allowed per 10s.

  // Getting the API key from a .env file.
  public function __construct()
  {
    try {
      $envFile = __DIR__ . '/.env';
      if (!file_exists($envFile)) {
        throw new Exception("The .env file is missing, please create one and add your API_KEY.\n");
      }
      $dotEnv = parse_ini_file(__DIR__ . '/.env');
      $this->key = $dotEnv['API_KEY'] ?? throw new Exception("API_KEY key is missing in .env file.\n");
    } catch (Exception $error) {
      echo $error->getMessage();
      die();
    }
  }

  // Main function that gets called and fetches chosen data from the API.
  function getDataFromApi($endpoint)
  {
    try {
      $url = $this->BASEURL . '/' . $this->VERSION . '/' . $endpoint;
      $context = stream_context_create([
        'http' => [
          'ignore_errors' => true
        ]
      ]);
      $response = file_get_contents($url, false, $context);
      $httpCode = explode(' ', $http_response_header[0])[1];
      $httpResponse = explode(' ', $http_response_header[0])[2];
      if ($httpCode == '200') {
        $result = json_decode($response, true);
        sleep($this->SLEEP);
        return $result;
      } elseif ($httpCode == '418') {
        throw new Exception("API request failed with HTTP status code 418 (No global access)\n");
      } else {
        throw new Exception("API request failed with HTTP status code $httpCode ($httpResponse)\n");
      }
    } catch (Exception $error) {
      echo $error->getMessage();
      die();
    }
  }

  // Returns all Instruments or Instrument Meta.
  function getAllInstruments($instruments)
  {
    $endpoint = "$instruments?authKey=$this->key";
    return $this->getDataFromApi($endpoint);
  }

  // Returns all Global Instruments.
  function getAllGlobalInstruments()
  {
    $endpoint = "instruments/global?authKey=$this->key";
    return $this->getDataFromApi($endpoint);
  }

  // Returns all Updated Instruments.
  function getAllUpdatedInstruments()
  {
    $endpoint = "instruments/updated?authKey=$this->key";
    return $this->getDataFromApi($endpoint);
  }

  // Returns Kpis History.
  function getKpiHistory($insId, $kpiId, $reportType, $priceType, $maxCount)
  {
    $endpoint = "instruments/$insId/kpis/$kpiId/$reportType/$priceType/history?authKey=$this->key&maxCount=$maxCount";
    return $this->getDataFromApi($endpoint);
  }

  // Returns Kpis summary list.
  function getKpiSummary($insId, $reportType, $maxCount)
  {
    $endpoint = "instruments/$insId/kpis/$reportType/summary?authKey=$this->key&maxCount=$maxCount";
    return $this->getDataFromApi($endpoint);
  }

  // Returns historical Kpis Data from list of Instruments.
  function getHistoricalKpis($kpiId, $reportType, $priceType, $instList)
  {
    $endpoint = "instruments/kpis/$kpiId/$reportType/$priceType/history?authKey=$this->key&instList=$instList";
    return $this->getDataFromApi($endpoint);
  }

  // Returns Kpis Data for one Instrument.
  function getKpidataForOneInstrument($insId, $kpiId, $calcGroup, $calc)
  {
    $endpoint = "instruments/$insId/kpis/$kpiId/$calcGroup/$calc?authKey=$this->key";
    return $this->getDataFromApi($endpoint);
  }

  // Returns Kpis Data for all Instruments.
  function getKpidataForAllInstruments($kpiId, $calcGroup, $calc)
  {
    $endpoint = "instruments/kpis/$kpiId/$calcGroup/$calc?authKey=$this->key";
    return $this->getDataFromApi($endpoint);
  }

  // Returns Kpis Data for all Global Instruments.
  function getKpidataForAllGlobalInstruments($kpiId, $calcGroup, $calc)
  {
    $endpoint = "instruments/global/kpis/$kpiId/$calcGroup/$calc?authKey=$this->key";
    return $this->getDataFromApi($endpoint);
  }

  // Returns Kpis Calculation DateTime.
  function getKpiUpdated()
  {
    $endpoint = "instruments/kpis/updated?authKey=$this->key";
    return $this->getDataFromApi($endpoint);
  }

  // Returns Kpis metadata.
  function getKpiMetadata()
  {
    $endpoint = "instruments/kpis/metadata?authKey=$this->key";
    return $this->getDataFromApi($endpoint);
  }

  // Returns Reports for one Instrument.
  function getReportsByType($insId, $reportType, $maxCount)
  {
    $endpoint = "instruments/$insId/reports/$reportType?authKey=$this->key&maxCount=$maxCount";
    return $this->getDataFromApi($endpoint);
  }

  // Returns Reports for one Instrument, All Reports Type included. (year, r12, quarter)
  function getReportsForAllTypes($insId, $maxYearCount, $maxR12QCount)
  {
    if (!empty($maxYearCount) && empty($maxR12QCount)) {
      $endpoint = "instruments/$insId/reports?authKey=$this->key&$maxYearCount";
    } elseif (empty($maxYearCount) && !empty($maxR12QCount)) {
      $endpoint = "instruments/$insId/reports?authKey=$this->key&$maxR12QCount";
    } elseif (!empty($maxYearCount) && !empty($maxR12QCount)) {
      $endpoint = "instruments/$insId/reports?authKey=$this->key&$maxYearCount&$maxR12QCount";
    } else {
      $endpoint = "instruments/$insId/reports?authKey=$this->key";
    }
    return $this->getDataFromApi($endpoint);
  }

  // Returns all Reports from list of Instruments.
  function getAllReports($instList, $maxYearCount, $maxR12QCount)
  {
    if (!empty($maxYearCount) && empty($maxR12QCount)) {
      $endpoint = "instruments/reports?authKey=$this->key&instList=$instList&maxYearCount=$maxYearCount";
    } elseif (empty($maxYearCount) && !empty($maxR12QCount)) {
      $endpoint = "instruments/reports?authKey=$this->key&instList=$instList&maxR12QCount=$maxR12QCount";
    } elseif (!empty($maxYearCount) && !empty($maxR12QCount)) {
      $endpoint = "instruments/reports?authKey=$this->key&instList=$instList&maxYearCount=$maxYearCount&maxR12QCount=$maxR12QCount";
    } else {
      $endpoint = "instruments/reports?authKey=$this->key&instList=$instList";
    }
    return $this->getDataFromApi($endpoint);
  }

  // Returns Report metadata.
  function getReportsMetadata()
  {
    $endpoint = "instruments/reports/metadata?authKey=$this->key";
    return $this->getDataFromApi($endpoint);
  }

  // Returns StockPrices for one Instrument.
  function getStockpricesForInstrument($insId, $from, $to, $maxCount)
  {
    if (!empty($from) && empty($to)) {
      $endpoint = "instruments/$insId/stockprices?authKey=$this->key&from=$from&maxCount=$maxCount";
    } elseif (empty($from) && !empty($to)) {
      $endpoint = "instruments/$insId/stockprices?authKey=$this->key&to=$to&maxCount=$maxCount";
    } elseif (!empty($from) && !empty($to)) {
      $endpoint = "instruments/$insId/stockprices?authKey=$this->key&from=$from&to=$to&maxCount=$maxCount";
    } else {
      $endpoint = "instruments/$insId/stockprices?authKey=$this->key&maxCount=$maxCount";
    }
    return $this->getDataFromApi($endpoint);
  }

  // Returns Last StockPrices for all Instruments.
  function getLastStockprices()
  {
    $endpoint = "instruments/stockprices/last?authKey=$this->key";
    return $this->getDataFromApi($endpoint);
  }

  // Returns Last StockPrices for all Global Instruments.
  function getLastGlobalStockprices()
  {
    $endpoint = "instruments/stockprices/global/last?authKey=$this->key";
    return $this->getDataFromApi($endpoint);
  }

  // Returns one StockPrice for each Instrument for a specific date.
  function getStockpricesForDate($date)
  {
    $endpoint = "instruments/stockprices/date?authKey=$this->key&date=$date";
    return $this->getDataFromApi($endpoint);
  }

  // Returns one StockPrice for each Global Instrument for a specific date.
  function getGlobalStockpricesForDate($date)
  {
    $endpoint = "instruments/stockprices/global/date?authKey=$this->key&date=$date";
    return $this->getDataFromApi($endpoint);
  }

  // Returns historical StockPrices from list of Instruments.
  function getHistoricalStockprices($instList, $from, $to)
  {
    if (!empty($from) && empty($to)) {
      $endpoint = "instruments/stockprices?authKey=$this->key&instList=$instList&from=$from";
    } elseif (empty($from) && !empty($to)) {
      $endpoint = "instruments/stockprices?authKey=$this->key&instList=$instList&to=$to";
    } elseif (!empty($from) && !empty($to)) {
      $endpoint = "instruments/stockprices?authKey=$this->key&instList=$instList&from=$from&to=$to";
    } else {
      $endpoint = "instruments/stockprices?authKey=$this->key&instList=$instList";
    }
    return $this->getDataFromApi($endpoint);
  }

  // Returns Stock Splits for all Instruments.
  function getStocksplits()
  {
    $endpoint = "instruments/StockSplits?authKey=$this->key";
    return $this->getDataFromApi($endpoint);
  }
}
