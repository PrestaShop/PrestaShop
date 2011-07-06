<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');

header("Status: 301 Moved Permanently", false, 301);
Header('Location: http://api.treepodia.com/sitemap/'.Configuration::get('TREEPODIA_ACCOUNT_CODE').'/sitemap.xml');
