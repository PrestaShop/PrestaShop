<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/gapi.php');

$gapi = new Gapi();
$gapi->api_3_0_oauth2callback();
