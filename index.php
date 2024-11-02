<?php
require_once'SuiviColis.php';
use TestRecrutement\SuiviColis\SuiviColis;


$suiviColis = new SuiviColis();
$suiviColis->deliveryState('aze@exemple.com',42);

