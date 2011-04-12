<?php

define('RELATIVE_PATH', dirname(__FILE__).'/');
define('FIANET_ENCODING','ISO-8859-1');

define('FIANET_SAC_SITE_ID', Configuration::get('SAC_SITEID'));
define('FIANET_SAC_LOGIN', Configuration::get('SAC_LOGIN'));
define('FIANET_SAC_PWD', Configuration::get('SAC_PASSWORD'));

define('FIANET_RNP_KEY', '');

require_once(RELATIVE_PATH . 'to_implement/implement.php');
require_once(RELATIVE_PATH . 'const/fianet.php');
require_once(RELATIVE_PATH . 'const/url_sac.php');
require_once(RELATIVE_PATH . 'const/url_rnp.php');

require_once(RELATIVE_PATH . 'includes/debug.php');
require_once(RELATIVE_PATH . 'includes/general.php');
require_once(RELATIVE_PATH . 'includes/xml2array.php');

if (!class_exists('HashMd5', false))
{
	require_once(RELATIVE_PATH . 'classes/fianet_key_64bits.php');
	//require_once(RELATIVE_PATH . 'classes/fianet_key_32bits.php');
}
require_once(RELATIVE_PATH . 'classes/fianet_sender.php');
require_once(RELATIVE_PATH . 'classes/fianet_socket.php');
require_once(RELATIVE_PATH . 'classes/order/fianet_adress_xml.php');
require_once(RELATIVE_PATH . 'classes/order/fianet_billing_adress_xml.php');
require_once(RELATIVE_PATH . 'classes/order/fianet_delivery_adress_xml.php');
require_once(RELATIVE_PATH . 'classes/order/fianet_appartment_xml.php');
require_once(RELATIVE_PATH . 'classes/order/fianet_user_xml.php');
require_once(RELATIVE_PATH . 'classes/order/fianet_billing_user_xml.php');
require_once(RELATIVE_PATH . 'classes/order/fianet_delivery_user_xml.php');
require_once(RELATIVE_PATH . 'classes/order/fianet_info_order_xml.php');
require_once(RELATIVE_PATH . 'classes/order/fianet_payment_xml.php');
require_once(RELATIVE_PATH . 'classes/order/fianet_product_list_xml.php');
require_once(RELATIVE_PATH . 'classes/order/fianet_product_xml.php');
require_once(RELATIVE_PATH . 'classes/order/fianet_transport_xml.php');
require_once(RELATIVE_PATH . 'classes/order/fianet_user_siteconso_xml.php');
require_once(RELATIVE_PATH . 'classes/order/fianet_order_xml.php');

require_once(RELATIVE_PATH . 'classes/order/fianet_rnp_order_xml.php');
require_once(RELATIVE_PATH . 'classes/order/fianet_rnp_info_order_xml.php');
require_once(RELATIVE_PATH . 'classes/order/fianet_rnp_wallet_xml.php');
require_once(RELATIVE_PATH . 'classes/order/fianet_xml_paracallback_builder.php');
require_once(RELATIVE_PATH . 'classes/order/fianet_paraobject_xml.php');


