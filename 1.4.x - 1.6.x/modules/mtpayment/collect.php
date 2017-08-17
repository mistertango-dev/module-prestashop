<?php

require_once(dirname(__FILE__) . '/../../config/config.inc.php');
require_once(dirname(__FILE__) . '/mttools.php');
require_once(dirname(__FILE__) . '/mtpayment.php');

$username = Configuration::get('MRTANGO_USERNAME');
$secretKey = Configuration::get('MRTANGO_SECRET_KEY');

if (!empty($username)) {
    MTConfiguration::updateUsername($username);
    echo 'Username was transfered.<br />';
}

if (!empty($secretKey)) {
    MTConfiguration::updateSecretKey($secretKey);
    echo 'Secret key was transfered.<br />';
}
