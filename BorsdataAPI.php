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

  private $BASEURL = 'https://apiservice.borsdata.se';
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

  /** This method calls Borsdata API.
   * @param string $requestUrl API request URL.
   * @return object JSON data.
   * @throws error API error.
   */
  function getDataFromApi($requestUrl)
  {
    try {
      $url = $this->BASEURL . '/' . $this->VERSION . '/' . $requestUrl;
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

  /** This method returns all nordic instruments or metadata. \
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
   * @return object JSON data.
   * @link https://github.com/Borsdata-Sweden/API/wiki/Instruments
   */
  function getAllInstruments($option)
  {
    $requestUrl = "$option?authKey=$this->key";
    return $this->getDataFromApi($requestUrl);
  }

  /** This method returns all global instruments. (Require Pro+)
   * @return object JSON data.
   * @link https://github.com/Borsdata-Sweden/API/wiki/Instruments
   */
  function getAllGlobalInstruments()
  {
    $requestUrl = "instruments/global?authKey=$this->key";
    return $this->getDataFromApi($requestUrl);
  }

  /** This method returns all updated nordic instruments.
   * @return object JSON data.
   * @link https://github.com/Borsdata-Sweden/API/wiki/Instruments
   */
  function getAllUpdatedInstruments()
  {
    $requestUrl = "instruments/updated?authKey=$this->key";
    return $this->getDataFromApi($requestUrl);
  }

  /** This method returns kpi history for the nordic market.
   * @param int $insId Instrument id. (Get all different id's with the "getAllInstruments('instruments')" method.)
   * @param int $kpiId Kpi id. (Get all different id's with the "getKpiMetadata()" method.)
   * @param string $reportType Report type. (year, r12, quarter)
   * @param string $priceType Price type. (low, mean or high)
   * @param int $maxCount Max number of results returned. (Max Year=20, R12&Quarter=40) (optional, can be empty)
   * @return object JSON data.
   * @link https://github.com/Borsdata-Sweden/API/wiki/KPI-History
   */
  function getKpiHistory($insId, $kpiId, $reportType, $priceType, $maxCount)
  {
    $requestUrl = "instruments/$insId/kpis/$kpiId/$reportType/$priceType/history?authKey=$this->key&maxCount=$maxCount";
    return $this->getDataFromApi($requestUrl);
  }

  /** This method returns a kpi summary list for one instrument in the nordic market.
   * @param int $insId Instrument id. (Get all different id's with the "getAllInstruments('instruments')" method.)
   * @param string $reportType Report type. (year, r12, quarter)
   * @param int $maxCount Max number of results returned. (optional, can be empty)
   * @return object JSON data.
   * @link https://github.com/Borsdata-Sweden/API/wiki/KPI-History
   */
  function getKpiSummary($insId, $reportType, $maxCount)
  {
    $requestUrl = "instruments/$insId/kpis/$reportType/summary?authKey=$this->key&maxCount=$maxCount";
    return $this->getDataFromApi($requestUrl);
  }

  /** This method returns kpi history for a list of instruments in the nordic market.
   * @param int $kpiId Kpi id. (Get all different id's with the "getKpiMetadata()" method.)
   * @param string $reportType Report type. (year, r12, quarter)
   * @param string $priceType Price type. (low, mean or high)
   * @param string $instList Comma separated list of instrument id's. (Max 50)  (Get all different id's with the getAllInstruments('instruments') method.)
   * @param int $maxCount Max number of results returned. (Max Year=20, R12&Quarter=40) (optional, can be empty)
   * @return object JSON data.
   * @link https://github.com/Borsdata-Sweden/API/wiki/KPI-History
   */
  function getHistoricalKpis($kpiId, $reportType, $priceType, $instList)
  {
    $requestUrl = "instruments/kpis/$kpiId/$reportType/$priceType/history?authKey=$this->key&instList=$instList";
    return $this->getDataFromApi($requestUrl);
  }

  /** This method returns kpi data for one instrument in the nordic market.
   * @param int $insId Instrument id. (Get all different id's with the getAllInstruments('instruments') method.)
   * @param int $kpiId Kpi id. (Get all different id's with the "getKpiMetadata()" method.)
   * @param string $calcGroup Kpi calculation group. Mainly based on time.
   * @param string $calc Kpi calculation.
   * @return object JSON data.
   * @link https://github.com/Borsdata-Sweden/API/wiki/KPI-Screener
   */
  function getKpidataForOneInstrument($insId, $kpiId, $calcGroup, $calc)
  {
    $requestUrl = "instruments/$insId/kpis/$kpiId/$calcGroup/$calc?authKey=$this->key";
    return $this->getDataFromApi($requestUrl);
  }

  /** This method returns kpi data for all instruments in the nordic market.
   * @param int $kpiId Kpi id. (Get all different id's with the "getKpiMetadata()" method.)
   * @param string $calcGroup Kpi calculation group. Mainly based on time.
   * @param string $calc Kpi calculation.
   * @return object JSON data.
   * @link https://github.com/Borsdata-Sweden/API/wiki/KPI-Screener
   */
  function getKpidataForAllInstruments($kpiId, $calcGroup, $calc)
  {
    $requestUrl = "instruments/kpis/$kpiId/$calcGroup/$calc?authKey=$this->key";
    return $this->getDataFromApi($requestUrl);
  }

  /** This method returns kpi data for all global instruments. (Require Pro+)
   * @param int $kpiId Kpi id. (Get all different id's with the "getKpiMetadata()" method.)
   * @param string $calcGroup Kpi calculation group. Mainly based on time.
   * @param string $calc Kpi calculation.
   * @return object JSON data.
   * @link https://github.com/Borsdata-Sweden/API/wiki/KPI-Screener
   */
  function getKpidataForAllGlobalInstruments($kpiId, $calcGroup, $calc)
  {
    $requestUrl = "instruments/global/kpis/$kpiId/$calcGroup/$calc?authKey=$this->key";
    return $this->getDataFromApi($requestUrl);
  }

  /** This method returns nordic kpi last updated calculation dateTime.
   * @return object JSON data.
   * @link https://github.com/Borsdata-Sweden/API/wiki/KPI-Screener
   */
  function getKpiUpdated()
  {
    $requestUrl = "instruments/kpis/updated?authKey=$this->key";
    return $this->getDataFromApi($requestUrl);
  }

  /** This method returns kpi metadata.
   * @return object JSON data.
   * @link https://github.com/Borsdata-Sweden/API/wiki/KPI-Screener
   */
  function getKpiMetadata()
  {
    $requestUrl = "instruments/kpis/metadata?authKey=$this->key";
    return $this->getDataFromApi($requestUrl);
  }

  /** This method returns reports for one instrument with one report type for the nordic market.
   * @param int $insId Instrument id. (Get all different id's with the "getAllInstruments('instruments')" method.)
   * @param string $reportType Report type. (year, r12, quarter)
   * @param int $maxCount Max number of results returned. (Max Year=20, R12&Quarter=40) (optional, can be empty)
   * @return object JSON data.
   * @link https://github.com/Borsdata-Sweden/API/wiki/Reports
   */
  function getReportsByType($insId, $reportType, $maxCount)
  {
    $requestUrl = "instruments/$insId/reports/$reportType?authKey=$this->key&maxCount=$maxCount";
    return $this->getDataFromApi($requestUrl);
  }

  /** This method returns reports for one instrument with all report types included. (year, r12, quarter)
   * @param int $insId Instrument id. (Get all different id's with the "getAllInstruments('instruments')" method.)
   * @param int $maxYearCount Max number of year reports returned. (10 year default, max 20)
   * @param int $maxR12QCount Max number of r12 and quarter reports returned. (10 default, max 40)
   * @return object JSON data.
   * @link https://github.com/Borsdata-Sweden/API/wiki/Reports
   */
  function getReportsForAllTypes($insId, $maxYearCount, $maxR12QCount)
  {
    if (!empty($maxYearCount) && empty($maxR12QCount)) {
      $requestUrl = "instruments/$insId/reports?authKey=$this->key&$maxYearCount";
    } elseif (empty($maxYearCount) && !empty($maxR12QCount)) {
      $requestUrl = "instruments/$insId/reports?authKey=$this->key&$maxR12QCount";
    } elseif (!empty($maxYearCount) && !empty($maxR12QCount)) {
      $requestUrl = "instruments/$insId/reports?authKey=$this->key&$maxYearCount&$maxR12QCount";
    } else {
      $requestUrl = "instruments/$insId/reports?authKey=$this->key";
    }
    return $this->getDataFromApi($requestUrl);
  }

  /** This method returns report metadata for the nordic market.
   * @return object JSON data.
   * @link https://github.com/Borsdata-Sweden/API/wiki/Reports
   */
  function getReportsMetadata()
  {
    $requestUrl = "instruments/reports/metadata?authKey=$this->key";
    return $this->getDataFromApi($requestUrl);
  }

  /** This method returns reports for list of instruments with all report types included. (year, r12, quarter)
   * @param string $instList Comma separated list of instrument id's. (Max 50) (Get all different id's with the "getAllInstruments('instruments')" method.)
   * @param int $maxYearCount Max number of year reports returned. (10 year default, max 20)
   * @param int $maxR12QCount Max number of r12 and quarter reports returned. (10 default, max 40)
   * @return object JSON data.
   * @link https://github.com/Borsdata-Sweden/API/wiki/Reports
   */
  function getAllReports($instList, $maxYearCount, $maxR12QCount)
  {
    if (!empty($maxYearCount) && empty($maxR12QCount)) {
      $requestUrl = "instruments/reports?authKey=$this->key&instList=$instList&maxYearCount=$maxYearCount";
    } elseif (empty($maxYearCount) && !empty($maxR12QCount)) {
      $requestUrl = "instruments/reports?authKey=$this->key&instList=$instList&maxR12QCount=$maxR12QCount";
    } elseif (!empty($maxYearCount) && !empty($maxR12QCount)) {
      $requestUrl = "instruments/reports?authKey=$this->key&instList=$instList&maxYearCount=$maxYearCount&maxR12QCount=$maxR12QCount";
    } else {
      $requestUrl = "instruments/reports?authKey=$this->key&instList=$instList";
    }
    return $this->getDataFromApi($requestUrl);
  }

  /** This method returns stockprices for one instrument between two dates for the nordic market.
   * @param int $insId Instrument id. (Get all different id's with the "getAllInstruments('instruments')" method.)
   * @param string $from From date. (YYYY-MM-DD) (optional, can be empty)
   * @param string $to To date. (YYYY-MM-DD) (optional, can be empty)
   * @param int $maxCount Max number of results returned. (Max 20) (optional, can be empty)
   * @return object JSON data.
   * @link https://github.com/Borsdata-Sweden/API/wiki/Stockprice
   */
  function getStockpricesForInstrument($insId, $from, $to, $maxCount)
  {
    if (!empty($from) && empty($to)) {
      $requestUrl = "instruments/$insId/stockprices?authKey=$this->key&from=$from&maxCount=$maxCount";
    } elseif (empty($from) && !empty($to)) {
      $requestUrl = "instruments/$insId/stockprices?authKey=$this->key&to=$to&maxCount=$maxCount";
    } elseif (!empty($from) && !empty($to)) {
      $requestUrl = "instruments/$insId/stockprices?authKey=$this->key&from=$from&to=$to&maxCount=$maxCount";
    } else {
      $requestUrl = "instruments/$insId/stockprices?authKey=$this->key&maxCount=$maxCount";
    }
    return $this->getDataFromApi($requestUrl);
  }

  /** This method returns stockprices for a list of instruments between two dates for the nordic market.
   * @param string $instList Comma separated list of instrument id's. (Max 50) (Get all different id's with the "getAllInstruments('instruments')" method.)
   * @param string $from From date. (YYYY-MM-DD) (optional, can be empty)
   * @param string $to To date. (YYYY-MM-DD) (optional, can be empty)
   * @return object JSON data.
   * @link https://github.com/Borsdata-Sweden/API/wiki/Stockprice
   */
  function getStockPricesForListOfInstruments($instList, $from, $to)
  {
    if (!empty($from) && empty($to)) {
      $requestUrl = "instruments/stockprices?authKey=$this->key&instList=$instList&from=$from";
    } elseif (empty($from) && !empty($to)) {
      $requestUrl = "instruments/stockprices?authKey=$this->key&instList=$instList&to=$to";
    } elseif (!empty($from) && !empty($to)) {
      $requestUrl = "instruments/stockprices?authKey=$this->key&instList=$instList&from=$from&to=$to";
    } else {
      $requestUrl = "instruments/stockprices?authKey=$this->key&instList=$instList";
    }
    return $this->getDataFromApi($requestUrl);
  }

  /** This method returns last stockprices for all nordic instruments.
   * @return object JSON data.
   * @link https://github.com/Borsdata-Sweden/API/wiki/Stockprice
   */
  function getLastStockprices()
  {
    $requestUrl = "instruments/stockprices/last?authKey=$this->key";
    return $this->getDataFromApi($requestUrl);
  }

  /** This method returns last/latest stockprices for all global instruments. Only Global(Pro+)
   * @return object JSON data.
   * @link https://github.com/Borsdata-Sweden/API/wiki/Stockprice
   */
  function getLastGlobalStockprices()
  {
    $requestUrl = "instruments/stockprices/global/last?authKey=$this->key";
    return $this->getDataFromApi($requestUrl);
  }

  /** This method returns one stock price for each nordic instrument on a specific date.
   * @param string $date Date. (YYYY-MM-DD)
   * @return object JSON data.
   * @link https://github.com/Borsdata-Sweden/API/wiki/Stockprice
   */
  function getStockpricesForDate($date)
  {
    $requestUrl = "instruments/stockprices/date?authKey=$this->key&date=$date";
    return $this->getDataFromApi($requestUrl);
  }

  /** This method returns one stock price for each global instrument on a specific date. Only Global(Pro+)
   * @param string $date Date. (YYYY-MM-DD)
   * @return object JSON data.
   * @link https://github.com/Borsdata-Sweden/API/wiki/Stockprice
   */
  function getGlobalStockpricesForDate($date)
  {
    $requestUrl = "instruments/stockprices/global/date?authKey=$this->key&date=$date";
    return $this->getDataFromApi($requestUrl);
  }

  /** This method returns stock splits for all nordic instruments. Max 1 year history.
   * @return object JSON data.
   */
  function getStocksplits()
  {
    $requestUrl = "instruments/StockSplits?authKey=$this->key";
    return $this->getDataFromApi($requestUrl);
  }
}
