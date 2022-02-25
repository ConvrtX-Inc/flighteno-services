<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use Twilio\Rest\Client;
class Mod_card extends CI_Model {
    
  function __construct() {
    parent::__construct();

  }

  public function getCardInfo($card_id) {   
    $db   =  $this->mongo_db->customQuery();    
    $history =  $db->card->find( array(  
        '_id' =>  $this->mongo_db->mongoId($card_id)
    ));
    $getData   =  iterator_to_array($history);

    return $getData;      
  }

  public function updateCard($card_id, $admin_id, $card_number, $expiry_date, $cvv, $card_name){
    $db = $this->mongo_db->customQuery();

    $update = [];
    if( !empty($admin_id) && !is_null($admin_id) ){
      $update['admin_id'] = $admin_id;
    }

    if(!empty($card_number) && !is_null($card_number) ){
      $update['card_number'] = $card_number;
    }

    if(!empty($expiry_date) && !is_null($expiry_date) ){
        $update['expiry_date'] = $expiry_date;
    }

    if(!empty($cvv) && !is_null($cvv) ){
        $update['cvv'] = $cvv;
    }
    
    if(!empty($card_name) && !is_null($card_name) ){
        $update['card_name'] = $card_name;
    }    

    if( !empty($update) && !is_null($update) ){

      $db->card->updateOne(['_id' => $this->mongo_db->mongoId($card_id) ], ['$set' => $update ] );
    }
    return true ;
  }

}