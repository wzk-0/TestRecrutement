<?php

namespace TestRecrutement\SuiviColis;

require_once 'config.php';
require_once'EmailHelper.php';

use TestRecrutement\SuiviColis\EmailHelper;

class SuiviColis{

    /**
     * 
     * Check the delivery state, send a mail accordingly and output all the events in a csv
     * 
     * @param string $mailClient Email address linked with the current tracked order
     * @param string $id id of the tracked order
     * 
     * @return void
    */ 
    public function deliveryState($mailClient,$id){
        $data = $this->fetchAPI($mailClient,$id);
        if($this->isDeliveryComplete($data['shipment']['event'])){
            $this->mailDeliveryComplete($mailClient,$id);
        }
        else{
            $this->mailDeliveryInProgress($mailClient,$id);
        }
        $this->exportToCSV($data['shipment']['event'],'6A58894858306');
    }

    /**
     * Send a https get request to LaPost API to obtain order tracking info
     *
     * @param string $id id of the tracked order
     * 
     * @return array response decoded in Json with the tracking info
    */ 
    public function fetchAPI($id){
        /* Initialization of CURL with some session option:
            CURLOPT_RETURNTRANSFER to get the result as string
            CURLINFO_HEADER_OUT in order to check the header
            CURLOPT_CAINFO to define the path to ssl cert
        */
        $curl = curl_init("https://api.laposte.fr/suivi/v2/idships/$id");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLINFO_HEADER_OUT, true);
        curl_setopt($curl, CURLOPT_CAINFO, dirname(__FILE__).DIRECTORY_SEPARATOR."cacert.pem");
        //set up of the request header
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Accept:application/json',
            'X-Okapi-Key: '.API_KEY,
        ]);

        $data=curl_exec($curl);
        //check if there is a direct error with cURL
        if($data === false)
        {
            throw new \Exception(curl_error($curl));
        }
        $code = curl_getinfo($curl,CURLINFO_HTTP_CODE );
        $data = json_decode($data, true);
        //check if response data is json
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Erreur decode JSON : ' . json_last_error_msg());
        }
        //error handling if the resonse code is not 200
        elseif($code != 200){
            throw new \Exception('Erreur API :'.$data['code'].$data['message']);
        }
        curl_close($curl);
        return $data;
    }

    /**
     * Send an email to inform about the current delivery status.
     *
     * @param string $mailClient recipient email
     * 
     * @return string $id tracked package id 
    */ 
    private function mailDeliveryInProgress($mailClient,$id){
        $message = "Bonjour,\nVotre colis n°$id est en route.";
        $mailHelper = new EmailHelper();
        $mailResult = $mailHelper->sendMail(
            $mailClient,
            "Suivi De Livraison",
            $message,
            "Livraison",
        );
        if($mailResult !== True) throw new \Exception("Erreur Email : $mailResult");
    }

    /**
     * Send an email to inform about the current delivery status.
     *
     * @param string $mailClient recipient email
     * 
     * @return int $id tracked package id 
    */ 
    private function mailDeliveryComplete($mailClient,$id){
        $message = "Bonjour,\nVotre colis n°$id a était livré.😊";
        $mailHelper = new EmailHelper();
        $mailResult = $mailHelper->sendMail(
            $mailClient,
            "Suivi De Livraison",
            $message,
            "Livraison",
            dirname(__FILE__).DIRECTORY_SEPARATOR."ressources".DIRECTORY_SEPARATOR."cartes-blanches.png"
        );
        if($mailResult !== True) throw new \Exception("Erreur Email : $mailResult");
    }

    /**
     *
     * @param array $event
     * 
     * @return bool 
    */ 
    private function isDeliveryComplete($event) {
        return isset($event[0]['code']) && $event[0]['code'] === 'DI1';
    }

    /**
     *Export the given array as an csv file named with the format date_id.csv
     * 
     * @param array $data
     * @param string $id
     * 
     * @return void
    */ 
    private function exportToCSV($data,$id){
        $file = fopen(__DIR__ . DIRECTORY_SEPARATOR . date("Ymdh").'_'.$id.'.csv', 'w');
        fputcsv($file, array_keys(reset($data)));
        foreach ($data as $row) {
            fputcsv($file, $row);
        }
        fclose($file);
    }
}


