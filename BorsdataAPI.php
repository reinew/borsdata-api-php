<?php

// All sample code is provided for illustrative purposes only.
// These examples have not been thoroughly tested under all conditions.
// The creator cannot guarantee or imply reliability, serviceability, or function of these programs.
// All programs contained herein are provided to you “AS IS” without any warranties of any kind.

class BorsdataAPI
{

  private $base_url = 'https://apiservice.borsdata.se';
  private $version = 'v1';
  private $key;
  private $sleep = 0.11; // Max 100 requests allowed per 10s.

  // Getting the API key from a .env file.
  public function __construct()
  {
    try {
      $envFile = __DIR__ . '/.env';
      if (!file_exists($envFile)) {
        throw new Exception("The .env file is missing, please create one and add your API_KEY.");
      }
      $dotEnv = parse_ini_file(__DIR__ . '/.env');
      $this->key = $dotEnv['API_KEY'] ?? throw new Exception("API_KEY key is missing in .env file.");
    } catch (Exception $e) {
      echo $e->getMessage() . "\n";
      die();
    }
  }

  // Main function that gets called and fetches chosen data from the API.
  function get_data_from_api($endpoint)
  {
    try {
      $url = $this->base_url . '/' . $this->version . '/' . $endpoint;
      $context = stream_context_create([
        'http' => [
          'ignore_errors' => true
        ]
      ]);
      $response = file_get_contents($url, false, $context);
      $httpCode = explode(' ', $http_response_header[0])[1];
      $httpError = explode(' ', $http_response_header[0])[2];
      if ($httpCode == '200') {
        $result = json_decode($response, true);
        sleep($this->sleep);
        return $result;
      } else {
        throw new Exception("API request failed with HTTP status code $httpCode ($httpError)\n");
      }
    } catch (Exception $e) {
      echo $e->getMessage();
      die();
    }
  }

  // Returns all Instruments or Instrument Meta.
  function get_all_instruments($instruments)
  {
    $endpoint = "$instruments?authKey=$this->key";
    return $this->get_data_from_api($endpoint);
  }

  // Returns all Global Instruments.
  function get_all_global_instruments()
  {
    $endpoint = "instruments/global?authKey=$this->key";
    return $this->get_data_from_api($endpoint);
  }

  // Returns all Updated Instruments.
  function get_all_updated_instruments()
  {
    $endpoint = "instruments/updated?authKey=$this->key";
    return $this->get_data_from_api($endpoint);
  }

  // Returns Kpis History.
  function get_kpi_history($insId, $kpiId, $reportType, $priceType, $maxCount)
  {
    $endpoint = "instruments/$insId/kpis/$kpiId/$reportType/$priceType/history?authKey=$this->key&maxCount=$maxCount";
    return $this->get_data_from_api($endpoint);
  }

  // Returns Kpis summary list.
  function get_kpi_summary($insId, $reportType, $maxCount)
  {
    $endpoint = "instruments/$insId/kpis/$reportType/summary?authKey=$this->key&maxCount=$maxCount";
    return $this->get_data_from_api($endpoint);
  }

  // Returns historical Kpis Data from list of Instruments.
  function get_historical_kpis($kpiId, $reportType, $priceType, $instList)
  {
    $endpoint = "instruments/kpis/$kpiId/$reportType/$priceType/history?authKey=$this->key&instList=$instList";
    return $this->get_data_from_api($endpoint);
  }

  // Returns Kpis Data for one Instrument.
  function get_kpidata_for_one_instrument($insId, $kpiId, $calcGroup, $calc)
  {
    $endpoint = "instruments/$insId/kpis/$kpiId/$calcGroup/$calc?authKey=$this->key";
    return $this->get_data_from_api($endpoint);
  }

  // Returns Kpis Data for all Instruments.
  function get_kpidata_for_all_instruments($kpiId, $calcGroup, $calc)
  {
    $endpoint = "instruments/kpis/$kpiId/$calcGroup/$calc?authKey=$this->key";
    return $this->get_data_from_api($endpoint);
  }

  // Returns Kpis Data for all Global Instruments.
  function get_kpidata_for_all_global_instruments($kpiId, $calcGroup, $calc)
  {
    $endpoint = "instruments/global/kpis/$kpiId/$calcGroup/$calc?authKey=$this->key";
    return $this->get_data_from_api($endpoint);
  }

  // Returns Kpis Calculation DateTime.
  function get_kpi_updated()
  {
    $endpoint = "instruments/kpis/updated?authKey=$this->key";
    return $this->get_data_from_api($endpoint);
  }

  // Returns Kpis metadata.
  function get_kpi_metadata()
  {
    $endpoint = "instruments/kpis/metadata?authKey=$this->key";
    return $this->get_data_from_api($endpoint);
  }

  // Returns Reports for one Instrument.
  function get_reports_by_type($insId, $reportType, $maxCount)
  {
    $endpoint = "instruments/$insId/reports/$reportType?authKey=$this->key&maxCount=$maxCount";
    return $this->get_data_from_api($endpoint);
  }

  // Returns Reports for one Instrument, All Reports Type included. (year, r12, quarter)
  function get_reports_for_all_types($insId, $maxYearCount, $maxR12QCount)
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
    return $this->get_data_from_api($endpoint);
  }

  // Returns all Reports from list of Instruments.
  function get_all_reports($instList, $maxYearCount, $maxR12QCount)
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
    return $this->get_data_from_api($endpoint);
  }

  // Returns Report metadata.
  function get_reports_metadata()
  {
    $endpoint = "instruments/reports/metadata?authKey=$this->key";
    return $this->get_data_from_api($endpoint);
  }

  // Returns StockPrices for one Instrument.
  function get_stockprices_for_instrument($insId, $from, $to, $maxCount)
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
    return $this->get_data_from_api($endpoint);
  }

  // Returns Last StockPrices for all Instruments.
  function get_last_stockprices()
  {
    $endpoint = "instruments/stockprices/last?authKey=$this->key";
    return $this->get_data_from_api($endpoint);
  }

  // Returns Last StockPrices for all Global Instruments.
  function get_last_global_stockprices()
  {
    $endpoint = "instruments/stockprices/global/last?authKey=$this->key";
    return $this->get_data_from_api($endpoint);
  }

  // Returns one StockPrice for each Instrument for a specific date.
  function get_stockprices_for_date($date)
  {
    $endpoint = "instruments/stockprices/date?authKey=$this->key&date=$date";
    return $this->get_data_from_api($endpoint);
  }

  // Returns one StockPrice for each Global Instrument for a specific date.
  function get_global_stockprices_for_date($date)
  {
    $endpoint = "instruments/stockprices/global/date?authKey=$this->key&date=$date";
    return $this->get_data_from_api($endpoint);
  }

  // Returns historical StockPrices from list of Instruments.
  function get_historical_stockprices($instList, $from, $to)
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
    return $this->get_data_from_api($endpoint);
  }

  // Returns Stock Splits for all Instruments.
  function get_stocksplits()
  {
    $endpoint = "instruments/StockSplits?authKey=$this->key";
    return $this->get_data_from_api($endpoint);
  }
}
