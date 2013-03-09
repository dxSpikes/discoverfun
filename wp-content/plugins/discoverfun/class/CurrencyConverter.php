<?php
 
/*
* File: CurrencyConverter.php
* Author: Simon Jarvis
* Copyright: 2005 Simon Jarvis
* Date: 10/12/05
* Link: http://www.white-hat-web-design.co.uk/articles/php-currency-conversion.php
 
*
* This program is free software; you can redistribute it and/or
* modify it under the terms of the GNU General Public License
* as published by the Free Software Foundation; either version 2
* of the License, or (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details:
* http://www.gnu.org/licenses/gpl.html
*
*/
 
class CurrencyConverter {
 
   var $xml_file = "www.ecb.int/stats/eurofxref/eurofxref-daily.xml";
   var $exchange_rates = array();
 
   //Load Currency Rates
 
   function CurrencyConverter() {
      global $wpdb;

 
      $this->checkLastUpdated();

 
      $sql = "SELECT * FROM ". $wpdb->prefix . "currency_converter";
 
      $rs =  $wpdb->get_results($sql,ARRAY_A);
 
      foreach($rs as $row){
 
         $this->exchange_rates[$row['currency']] = $row['rate'];
      }
 
   }
 
   /* Perform the actual conversion, defaults to £1.00 GBP to USD */
   function convert($amount=1,$from="GBP",$to="USD",$decimals=2) {
      $divsn = $this->exchange_rates[$from];
      $divsn = ($divsn == 0 )? 1 : $divsn; 
      return(number_format(($amount/$divsn)*$this->exchange_rates[$to],$decimals));
   }
 
   /* Check to see how long since the data was last updated */
   function checkLastUpdated() {
      global $wpdb;
      $sql = "SHOW TABLE STATUS LIKE '".$wpdb->prefix . "currency_converter'";
 
      $rs =  $wpdb->get_row($sql,ARRAY_A);
 
      if($wpdb->num_rows == 0 ) {
 
         $this->createTable();
      } else {
         if(time() > (strtotime($rs["Update_time"])+(12*60*60)) ) {
 
            $this->downloadExchangeRates();
         }
      }
   }
 
   /* Download xml file, extract exchange rates and store values in database */
 
   function downloadExchangeRates() {
      global $wpdb;
      $currency_domain = substr($this->xml_file,0,strpos($this->xml_file,"/"));
      $currency_file = substr($this->xml_file,strpos($this->xml_file,"/"));
      $fp = @fsockopen($currency_domain, 80, $errno, $errstr, 10);
      if($fp) {
 
         $out = "GET ".$currency_file." HTTP/1.1\r\n";
         $out .= "Host: ".$currency_domain."\r\n";
         $out .= "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8) Gecko/20051111 Firefox/1.5\r\n";
         $out .= "Connection: Close\r\n\r\n";
         fwrite($fp, $out);
         while (!feof($fp)) {
 
            $buffer .= fgets($fp, 128);
         }
         fclose($fp);
 
         $pattern = "{<Cube\s*currency='(\w*)'\s*rate='([\d\.]*)'/>}is";
         preg_match_all($pattern,$buffer,$xml_rates);
         array_shift($xml_rates);
 
         for($i=0;$i<count($xml_rates[0]);$i++) {
 
            $exchange_rate[$xml_rates[0][$i]] = $xml_rates[1][$i];
         }

 
         foreach($exchange_rate as $currency=>$rate) {
 
            if((is_numeric($rate)) && ($rate != 0)) {
 
               $sql = "SELECT * FROM ". $wpdb->prefix . "currency_converter WHERE currency= %s";
               $rs = $wpdb->query($wpdb->prepare($sql,$currency));

               if($wpdb->num_rows > 0) {
 
                  $sql = "UPDATE ".$wpdb->prefix . "currency_converter SET rate=".$rate." WHERE currency='".$currency."'";

               } else {
 
                  $sql = "INSERT INTO ".$wpdb->prefix . "currency_converter VALUES('".$currency."',".$rate.")";
               }
 
               $rs =  $wpdb->query($sql);
            }
 
         }
      }
   }
    /* Create the currency exchange table */
   function createTable() {

      global $wpdb;
      global $jal_db_version;

      $table_name = $wpdb->prefix . "currency_converter";
         
      $sql = "CREATE TABLE $table_name (
      currency char(3) NOT NULL default '',
      rate float NOT NULL default '0',
      PRIMARY KEY(currency)
      )ENGINE=innoDB;";

      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
      dbDelta($sql);
   }
}