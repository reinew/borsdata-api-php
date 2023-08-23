<?php

/**
 * @author ReineW
 * @license MIT
 * @link https://github.com/reinew/borsdata-api
 *
 * This class is a PHP wrapper for easily interacting with Borsdata API.
 *
 * This class have not been thoroughly tested under all conditions.
 * The creator cannot guarantee or imply reliability, serviceability, or function of this class.
 * All code contained herein are provided to you “AS IS” without any warranties of any kind.
 */

class BorsdataAPI
{

  private $BASE_URL = 'https://apiservice.borsdata.se';
  private $VERSION = 'v1';
  private $key; // API key.
  private $SLEEP = 0.11; // Max 100 requests allowed per 10s.

  /** Getting the API key from the .env file. */
  public function __construct()
  {
    try {
      $envFile = __DIR__ . '/.env';
      if (!file_exists($envFile)) {
        throw new Exception("The .env file is missing, please create one and add your API_KEY.\n");
      }
      $dotEnv = parse_ini_file($envFile);
      $this->key = $dotEnv['API_KEY'] ?? throw new Exception("API_KEY key is missing in .env file.\n");
    } catch (Exception $error) {
      echo $error->getMessage();
      die();
    }
  }

  /** This function calls Borsdata API.
   * @param string $requestUrl API request URL.
   * @return object array with JSON data.
   * @throws error API error.
   */
  function getDataFromApi(string $requestUrl)
  {
    $url = $this->BASE_URL . '/' . $this->VERSION . '/' . $requestUrl;

    $context = stream_context_create([
      'http' => [
        'ignore_errors' => true
      ]
    ]);

    try {
      $response = file_get_contents($url, false, $context);
      $statusCode = explode(' ', $http_response_header[0])[1];
      $responseText = explode(' ', $http_response_header[0])[2];

      if ($statusCode == '200') {
        $result = json_decode($response, true);
        sleep($this->SLEEP);
        return $result;
      } elseif ($statusCode == '418') {
        throw new Exception("API request failed with HTTP status code 418 (No global access)\n");
      } else {
        throw new Exception("API request failed with HTTP status code $statusCode ($responseText)\n");
      }
    } catch (Exception $error) {
      echo "API request failed: " . $error->getMessage() . "\n";
      die();
    }
  }

  /** This function returns all nordic instruments or metadata. \
   * \
   * Choose one of the following API options: \
   * 'instruments' - Returns all instruments. \
   * 'branches' - Returns all branches. \
   * 'countries' - Returns all countries for nordic. \
   * 'markets' - Returns all markets. \
   * 'sectors' - Returns all sectors. \
   * 'translationmetadata' - Returns language translations for bransch, sector and country.
   *
   * @param string $option API option.
   * @return object array with JSON data.
   * @link https://github.com/Borsdata-Sweden/API/wiki/Instruments
   */
  function getAllInstruments(string $option)
  {
    $queryParams = [
      'authKey' => $this->key,
    ];

    $requestUrl = "$option?" . http_build_query($queryParams);

    return $this->getDataFromApi($requestUrl);
  }

  /** This function returns all global instruments. (Require Pro+)
   * @return object array with JSON data.
   * @link https://github.com/Borsdata-Sweden/API/wiki/Instruments
   */
  function getAllGlobalInstruments()
  {
    $queryParams = [
      'authKey' => $this->key,
    ];

    $requestUrl = "instruments/global?" . http_build_query($queryParams);

    return $this->getDataFromApi($requestUrl);
  }

  /** This function returns all updated nordic instruments.
   * @return object array with JSON data.
   * @link https://github.com/Borsdata-Sweden/API/wiki/Instruments
   */
  function getAllUpdatedInstruments()
  {
    $queryParams = [
      'authKey' => $this->key,
    ];

    $requestUrl = "instruments/updated?" . http_build_query($queryParams);

    return $this->getDataFromApi($requestUrl);
  }

  /** This function returns kpi history for the nordic market.
   * @param int $insId Instrument id. (Get all different id's with the "getAllInstruments('instruments')" function.)
   * @param int $kpiId Kpi id. (Get all different id's with the "getKpiMetadata()" function.)
   * @param string $reportType Report type. (year, r12, quarter)
   * @param string $priceType Price type. (low, mean or high)
   * @param int $maxCount Max number of results returned. (Max Year=20, R12&Quarter=40) (optional)
   * @return object array with JSON data.
   * @link https://github.com/Borsdata-Sweden/API/wiki/KPI-History
   */
  function getKpiHistory(int $insId, int $kpiId, string $reportType, string $priceType, int $maxCount = null)
  {
    $queryParams = [
      'authKey' => $this->key,
    ];

    if ($maxCount !== null) {
      $queryParams['maxCount'] = $maxCount;
    }

    $requestUrl = "instruments/$insId/kpis/$kpiId/$reportType/$priceType/history?" . http_build_query($queryParams);

    return $this->getDataFromApi($requestUrl);
  }

  /** This function returns a kpi summary list for one instrument in the nordic market.
   * @param int $insId Instrument id. (Get all different id's with the "getAllInstruments('instruments')" function.)
   * @param string $reportType Report type. (year, r12, quarter)
   * @param int $maxCount Max number of results returned. (optional)
   * @return object array with JSON data.
   * @link https://github.com/Borsdata-Sweden/API/wiki/KPI-History
   */
  function getKpiSummary(int $insId, string $reportType, int $maxCount = null)
  {
    $queryParams = [
      'authKey' => $this->key,
    ];

    if ($maxCount !== null) {
      $queryParams['maxCount'] = $maxCount;
    }

    $requestUrl = "instruments/$insId/kpis/$reportType/summary?" . http_build_query($queryParams);

    return $this->getDataFromApi($requestUrl);
  }

  /** This function returns kpi history for a list of instruments in the nordic market.
   * @param int $kpiId Kpi id. (Get all different id's with the "getKpiMetadata()" function.)
   * @param string $reportType Report type. (year, r12, quarter)
   * @param string $priceType Price type. (low, mean or high)
   * @param string $instList Comma separated list of instrument id's. (Max 50)  (Get all different id's with the getAllInstruments('instruments') function.)
   * @param int $maxCount Max number of results returned. (Max Year=20, R12&Quarter=40) (optional)
   * @return object array with JSON data.
   * @link https://github.com/Borsdata-Sweden/API/wiki/KPI-History
   */
  function getHistoricalKpis(int $kpiId, string $reportType, string $priceType, string $instList, int $maxCount = null)
  {
    $queryParams = [
      'authKey' => $this->key,
      'instList' => $instList,
    ];

    if ($maxCount !== null) {
      $queryParams['maxCount'] = $maxCount;
    }

    $requestUrl = "instruments/kpis/$kpiId/$reportType/$priceType/history?" . http_build_query($queryParams);

    return $this->getDataFromApi($requestUrl);
  }

  /** This function returns kpi data for one instrument in the nordic market.
   * @param int $insId Instrument id. (Get all different id's with the getAllInstruments('instruments') function.)
   * @param int $kpiId Kpi id. (Get all different id's with the "getKpiMetadata()" function.)
   * @param string $calcGroup Kpi calculation group. Mainly based on time.
   * @param string $calc Kpi calculation.
   * @return object array with JSON data.
   * @link https://github.com/Borsdata-Sweden/API/wiki/KPI-Screener
   */
  function getKpidataForOneInstrument(int $insId, int $kpiId, string $calcGroup, string $calc)
  {
    $queryParams = [
      'authKey' => $this->key,
    ];

    $requestUrl = "instruments/$insId/kpis/$kpiId/$calcGroup/$calc?" . http_build_query($queryParams);

    return $this->getDataFromApi($requestUrl);
  }

  /** This function returns kpi data for all instruments in the nordic market.
   * @param int $kpiId Kpi id. (Get all different id's with the "getKpiMetadata()" function.)
   * @param string $calcGroup Kpi calculation group. Mainly based on time.
   * @param string $calc Kpi calculation.
   * @return object array with JSON data.
   * @link https://github.com/Borsdata-Sweden/API/wiki/KPI-Screener
   */
  function getKpidataForAllInstruments(int $kpiId, string $calcGroup, string $calc)
  {
    $queryParams = [
      'authKey' => $this->key,
    ];

    $requestUrl = "instruments/kpis/$kpiId/$calcGroup/$calc?" . http_build_query($queryParams);

    return $this->getDataFromApi($requestUrl);
  }

  /** This function returns kpi data for all global instruments. (Require Pro+)
   * @param int $kpiId Kpi id. (Get all different id's with the "getKpiMetadata()" function.)
   * @param string $calcGroup Kpi calculation group. Mainly based on time.
   * @param string $calc Kpi calculation.
   * @return object array with JSON data.
   * @link https://github.com/Borsdata-Sweden/API/wiki/KPI-Screener
   */
  function getKpidataForAllGlobalInstruments(int $kpiId, string $calcGroup, string $calc)
  {
    $queryParams = [
      'authKey' => $this->key,
    ];

    $requestUrl = "instruments/global/kpis/$kpiId/$calcGroup/$calc?" . http_build_query($queryParams);

    return $this->getDataFromApi($requestUrl);
  }

  /** This function returns nordic kpi last updated calculation dateTime.
   * @return object array with JSON data.
   * @link https://github.com/Borsdata-Sweden/API/wiki/KPI-Screener
   */
  function getKpiUpdated()
  {
    $queryParams = [
      'authKey' => $this->key,
    ];

    $requestUrl = "instruments/kpis/updated?" . http_build_query($queryParams);

    return $this->getDataFromApi($requestUrl);
  }

  /** This function returns kpi metadata.
   * @return object array with JSON data.
   * @link https://github.com/Borsdata-Sweden/API/wiki/KPI-Screener
   */
  function getKpiMetadata()
  {
    $queryParams = [
      'authKey' => $this->key,
    ];

    $requestUrl = "instruments/kpis/metadata?" . http_build_query($queryParams);

    return $this->getDataFromApi($requestUrl);
  }

  /** This function returns reports for one instrument with one report type for the nordic market.
   * @param int $insId Instrument id. (Get all different id's with the "getAllInstruments('instruments')" function.)
   * @param string $reportType Report type. (year, r12, quarter)
   * @param int $maxCount Max number of results returned. (Max Year=20, R12&Quarter=40) (optional)
   * @return object array with JSON data.
   * @link https://github.com/Borsdata-Sweden/API/wiki/Reports
   */
  function getReportsByType(int $insId, string $reportType, int $maxCount = null)
  {
    $queryParams = [
      'authKey' => $this->key,
    ];

    if ($maxCount !== null) {
      $queryParams['maxCount'] = $maxCount;
    }

    $requestUrl = "instruments/$insId/reports/$reportType?" . http_build_query($queryParams);

    return $this->getDataFromApi($requestUrl);
  }

  /** This function returns reports for one instrument with all report types included. (year, r12, quarter)
   * @param int $insId Instrument id. (Get all different id's with the "getAllInstruments('instruments')" function.)
   * @param int $maxYearCount Max number of year reports returned. (10 year default, max 20) (Optional)
   * @param int $maxR12QCount Max number of r12 and quarter reports returned. (10 default, max 40) (Optional)
   * @return object array with JSON data.
   * @link https://github.com/Borsdata-Sweden/API/wiki/Reports
   */
  // function getReportsForAllTypes($insId, $maxYearCount, $maxR12QCount)
  function getReportsForAllTypes(int $insId, int $maxYearCount = null, int $maxR12QCount = null)
  {
    $queryParams = [
      'authKey' => $this->key,
    ];

    if ($maxYearCount !== null) {
      $queryParams['maxYearCount'] = $maxYearCount;
    }

    if ($maxR12QCount !== null) {
      $queryParams['maxR12QCount'] = $maxR12QCount;
    }

    $requestUrl = "instruments/$insId/reports?" . http_build_query($queryParams);

    return $this->getDataFromApi($requestUrl);
  }

  /** This function returns report metadata for the nordic market.
   * @return object array with JSON data.
   * @link https://github.com/Borsdata-Sweden/API/wiki/Reports
   */
  function getReportsMetadata()
  {
    $queryParams = [
      'authKey' => $this->key,
    ];

    $requestUrl = "instruments/reports/metadata?" . http_build_query($queryParams);

    return $this->getDataFromApi($requestUrl);
  }
  // {
  //   $requestUrl = "instruments/reports/metadata?authKey=$this->key";
  //   return $this->getDataFromApi($requestUrl);
  // }

  /** This function returns reports for list of instruments with all report types included. (year, r12, quarter)
   * @param string $instList Comma separated list of instrument id's. (Max 50) (Get all different id's with the "getAllInstruments('instruments')" function.)
   * @param int $maxYearCount Max number of year reports returned. (10 year default, max 20) (Optional)
   * @param int $maxR12QCount Max number of r12 and quarter reports returned. (10 default, max 40) (Optional)
   * @return object array with JSON data.
   * @link https://github.com/Borsdata-Sweden/API/wiki/Reports
   */
  function getAllReports(string $instList, int $maxYearCount = null, int $maxR12QCount = null)
  {
    $queryParams = [
      'authKey' => $this->key,
      'instList' => $instList,
    ];

    if ($maxYearCount !== null) {
      $queryParams['maxYearCount'] = $maxYearCount;
    }

    if ($maxR12QCount !== null) {
      $queryParams['maxR12QCount'] = $maxR12QCount;
    }

    $requestUrl = "instruments/reports?" . http_build_query($queryParams);

    return $this->getDataFromApi($requestUrl);
  }

  /** This function returns stockprices for one instrument between two dates for the nordic market.
   * @param int $insId Instrument id. (Get all different id's with the "getAllInstruments('instruments')" function.)
   * @param string $from From date. (YYYY-MM-DD) (optional)
   * @param string $to To date. (YYYY-MM-DD) (optional)
   * @param int $maxCount Max number of results returned. (Max 20) (optional)
   * @return object array with JSON data.
   * @link https://github.com/Borsdata-Sweden/API/wiki/Stockprice
   */
  function getStockpricesForInstrument(int $insId, string $from = null, string $to = null, int $maxCount = null)
  {
    $queryParams = [
      'authKey' => $this->key,
    ];

    if ($from !== null) {
      $queryParams['from'] = $from;
    }

    if ($to !== null) {
      $queryParams['to'] = $to;
    }

    if ($maxCount !== null) {
      $queryParams['maxCount'] = $maxCount;
    }

    $requestUrl = "instruments/$insId/stockprices?" . http_build_query($queryParams);

    return $this->getDataFromApi($requestUrl);
  }

  /** This function returns stockprices for a list of instruments between two dates for the nordic market.
   * @param string $instList Comma separated list of instrument id's. (Max 50) (Get all different id's with the "getAllInstruments('instruments')" function.)
   * @param string $from From date. (YYYY-MM-DD) (optional)
   * @param string $to To date. (YYYY-MM-DD) (optional)
   * @return object array with JSON data.
   * @link https://github.com/Borsdata-Sweden/API/wiki/Stockprice
   */
  function getStockPricesForListOfInstruments(string $instList, string $from = null, string $to = null)
  {
    $queryParams = [
      'authKey' => $this->key,
      'instList' => $instList,
    ];

    if ($from !== null) {
      $queryParams['from'] = $from;
    }

    if ($to !== null) {
      $queryParams['to'] = $to;
    }

    $requestUrl = "instruments/stockprices?" . http_build_query($queryParams);

    return $this->getDataFromApi($requestUrl);
  }

  /** This function returns last stockprices for all nordic instruments.
   * @return object array with JSON data.
   * @link https://github.com/Borsdata-Sweden/API/wiki/Stockprice
   */
  function getLastStockprices()
  {
    $queryParams = [
      'authKey' => $this->key,
    ];

    $requestUrl = "instruments/stockprices/last?" . http_build_query($queryParams);

    return $this->getDataFromApi($requestUrl);
  }

  /** This function returns last/latest stockprices for all global instruments. Only Global(Pro+)
   * @return object array with JSON data.
   * @link https://github.com/Borsdata-Sweden/API/wiki/Stockprice
   */
  function getLastGlobalStockprices()
  {
    $queryParams = [
      'authKey' => $this->key,
    ];

    $requestUrl = "instruments/stockprices/global/last?" . http_build_query($queryParams);

    return $this->getDataFromApi($requestUrl);
  }

  /** This function returns one stock price for each nordic instrument on a specific date.
   * @param string $date Date. (YYYY-MM-DD)
   * @return object array with JSON data.
   * @link https://github.com/Borsdata-Sweden/API/wiki/Stockprice
   */
  function getStockpricesForDate(string $date)
  {
    $queryParams = [
      'authKey' => $this->key,
      'date' => $date,
    ];

    $requestUrl = "instruments/stockprices/date?" . http_build_query($queryParams);

    return $this->getDataFromApi($requestUrl);
  }

  /** This function returns one stock price for each global instrument on a specific date. Only Global(Pro+)
   * @param string $date Date. (YYYY-MM-DD)
   * @return object array with JSON data.
   * @link https://github.com/Borsdata-Sweden/API/wiki/Stockprice
   */
  function getGlobalStockpricesForDate(string $date)
  {
    $queryParams = [
      'authKey' => $this->key,
      'date' => $date,
    ];

    $requestUrl = "instruments/stockprices/global/date?" . http_build_query($queryParams);

    return $this->getDataFromApi($requestUrl);
  }

  /** This function returns stock splits for all nordic instruments. Max 1 year history.
   * @return object array with JSON data.
   */
  function getStocksplits()
  {
    $queryParams = [
      'authKey' => $this->key,
    ];

    $requestUrl = "instruments/StockSplits?" . http_build_query($queryParams);

    return $this->getDataFromApi($requestUrl);
  }
}
