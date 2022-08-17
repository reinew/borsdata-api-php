<?php

// All sample code is provided for illustrative purposes only.
// These examples have not been thoroughly tested under all conditions.
// The creator cannot guarantee or imply reliability, serviceability, or function of these programs.
// All programs contained herein are provided to you “AS IS” without any warranties of any kind.

class BorsdataAPI {

    private $base_url = 'https://apiservice.borsdata.se';
    private $version = 'v1';
    private $key = ""; // Set the apikey by calling the set_apikey() function.
    private $sleep = 0.11; // Max 100 requests allowed per 10s.

    // Setting the API key.
    function set_apikey($apiKey) {
        $this->key = $apiKey;
    }

    // Main function that gets called and fetches choosen data from the API.
    function get_data_from_api($endpoint) {
        $url = $this->base_url.'/'.$this->version.'/'.$endpoint;
        $result = json_decode(file_get_contents($url), true);
        sleep($this->sleep);
        return $result;
    }

    // Returns all Instruments and Instrument Meta.
    function get_all_instruments($instrument) {
       $endpoint = "$instrument?authKey=$this->key";
        return $this->get_data_from_api($endpoint);
    }

    // Returns all Updated Instruments.
    function get_all_updated_instruments() {
        $endpoint = "instruments/updated?authKey=$this->key";
        return $this->get_data_from_api($endpoint);
    }

    // Returns Kpis History.
    function get_kpi_history($insId, $kpiId, $reportType, $priceType, $maxCount) {
        $endpoint = "instruments/$insId/kpis/$kpiId/$reportType/$priceType/history?authKey=$this->key&maxCount=$maxCount";
        return $this->get_data_from_api($endpoint);
    }

    // Returns Kpis summary list.
    function get_kpi_summary($insId, $reportType, $maxCount) {
        $endpoint = "instruments/$insId/kpis/$reportType/summary?authKey=$this->key&maxCount=$maxCount";
        return $this->get_data_from_api($endpoint);
    }

    // Returns historical Kpis Data from list of Instruments.
    function get_historical_kpis($kpiId, $reportType, $priceType, $instList) {
        $endpoint = "instruments/kpis/$kpiId/$reportType/$priceType/history?authKey=$this->key&instList=$instList";        
        return $this->get_data_from_api($endpoint);
    }

    // Returns Kpis Data for one Instrument.
    function get_kpidata_for_one_instrument($insId, $kpiId, $calcGroup, $calc) {
        $endpoint = "instruments/$insId/kpis/$kpiId/$calcGroup/$calc?authKey=$this->key";
        return $this->get_data_from_api($endpoint);
    }

    // Returns Kpis Data for all Instruments.
    function get_kpidata_for_all_instruments($kpiId, $calcGroup, $calc) {
        $endpoint = "instruments/kpis/$kpiId/$calcGroup/$calc?authKey=$this->key";
        return $this->get_data_from_api($endpoint);
    }

    // Returns Kpis Calculation DateTime.
    function get_kpi_updated() {
        $endpoint = "instruments/kpis/updated?authKey=$this->key";
        return $this->get_data_from_api($endpoint);
    }

    // Returns Kpis metadata.
    function get_kpi_metadata() {
        $endpoint = "instruments/kpis/metadata?authKey=$this->key";
        return $this->get_data_from_api($endpoint);
    }

    // Returns Reports for one Instrument.
    function get_reports_by_type($insId, $reportType, $maxCount) {
        $endpoint = "instruments/$insId/reports/$reportType?authKey=$this->key&maxCount=$maxCount";
        return $this->get_data_from_api($endpoint);
    }

    // Returns Reports for one Instrument, All Reports Type included. (year, r12, quarter)
    function get_reports_for_all_types($insId, $maxYearCount, $maxR12Count) {
        if (!empty($maxYearCount) && empty($maxR12Count)) {
            $endpoint = "instruments/$insId/reports?authKey=$this->key&$maxYearCount";
        } elseif (empty($maxYearCount) && !empty($maxR12Count)) {
            $endpoint = "instruments/$insId/reports?authKey=$this->key&$maxR12Count";
        } elseif (!empty($maxYearCount) && !empty($maxR12Count)) {
            $endpoint = "instruments/$insId/reports?authKey=$this->key&$maxYearCount&$maxR12Count";
        } else {
            $endpoint = "instruments/$insId/reports?authKey=$this->key";
        }
        return $this->get_data_from_api($endpoint);
    }

    // Returns all Reports from list of Instruments.
    function get_all_reports($instList) {
        $endpoint = "instruments/reports?authKey=$this->key&instList=$instList";
        return $this->get_data_from_api($endpoint);
    }

    // Returns Report metadata.
    function get_reports_metadata() {
        $endpoint = "instruments/reports/metadata?authKey=$this->key";
        return $this->get_data_from_api($endpoint);
    }

    // Returns StockPrices for one Instrument.
    function get_stockprices_for_instrument($insId, $from, $to, $maxCount) {
        if (!empty($from) && empty($to) ) {
            $endpoint = "instruments/$insId/stockprices?authKey=$this->key&from=$from&maxCount=$maxCount";
        } elseif (empty($from) && !empty($to) ) {
            $endpoint = "instruments/$insId/stockprices?authKey=$this->key&to=$to&maxCount=$maxCount";
        } elseif (!empty($from) && !empty($to) ) {
            $endpoint = "instruments/$insId/stockprices?authKey=$this->key&from=$from&to=$to&maxCount=$maxCount";
        } else {
            $endpoint = "instruments/$insId/stockprices?authKey=$this->key&maxCount=$maxCount";
        }
        return $this->get_data_from_api($endpoint);
    }

    // Returns Last StockPrices for all Instruments.
    function get_last_stockprices() {
        $endpoint = "instruments/stockprices/last?authKey=$this->key";
        return $this->get_data_from_api($endpoint);
    }

    // Returns one StockPrice for each Instrument for a specific date.
    function get_stockprices_for_date($date) {
        $endpoint = "instruments/stockprices/date?authKey=$this->key&date=$date";
        return $this->get_data_from_api($endpoint);
    }

    // Returns historical StockPrices from list of Instruments.
    function get_historical_stockprices($instList, $from, $to) {
        if (!empty($from) && empty($to) ) {
            $endpoint = "instruments/stockprices?authKey=$this->key&instList=$instList&from=$from";
        } elseif (empty($from) && !empty($to) ) {
            $endpoint = "instruments/stockprices?authKey=$this->key&instList=$instList&to=$to";
        } elseif (!empty($from) && !empty($to) ) {
            $endpoint = "instruments/stockprices?authKey=$this->key&instList=$instList&from=$from&to=$to";
        } else {
            $endpoint = "instruments/stockprices?authKey=$this->key&instList=$instList";
        }
        return $this->get_data_from_api($endpoint);
    }

    // Returns Stock Splits for all Instruments.
    function get_stocksplits() {
        $endpoint = "instruments/StockSplits?authKey=$this->key";
        return $this->get_data_from_api($endpoint);
    }
}

?>
