<?php
/*
* 2007-2011 PrestaShop 
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

require_once(dirname(__FILE__).'/../../config/config.inc.php');
require_once(dirname(__FILE__).'/gcheckout.php');
require_once(dirname(__FILE__).'/library/googleresponse.php');
require_once(dirname(__FILE__).'/library/googlemerchantcalculations.php');
require_once(dirname(__FILE__).'/library/googleresult.php');
require_once(dirname(__FILE__).'/library/googlerequest.php');
require_once(dirname(__FILE__).'/library/googlecart.php');


$merchant_id = Configuration::get('GCHECKOUT_MERCHANT_ID');
$merchant_key = Configuration::get('GCHECKOUT_MERCHANT_KEY');
$server_type = Configuration::get('GCHECKOUT_MODE');

$Gresponse = new GoogleResponse($merchant_id, $merchant_key);
//$Grequest = new GoogleRequest($merchant_id, $merchant_key, $server_type, $currency);

//Setup the log file
if (Configuration::get('GCHECKOUT_LOGS'))
	$Gresponse->SetLogFiles('googleerror.log', 'googlemessage.log', L_ALL);

// Retrieve the XML sent in the HTTP POST request to the ResponseHandler
$xml_response = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA:file_get_contents("php://input");
if (get_magic_quotes_gpc())
	$xml_response = stripslashes($xml_response);

list($root, $data) = $Gresponse->GetParsedXML($xml_response);
$Gresponse->SetMerchantAuthentication($merchant_id, $merchant_key);

$status = $Gresponse->HttpAuthentication();
if(!$status)
	die('authentication failed');
	
  /* Commands to send the various order processing APIs
   * Send charge order : $Grequest->SendChargeOrder($data[$root]
   *    ['google-order-number']['VALUE'], <amount>);
   * Send process order : $Grequest->SendProcessOrder($data[$root]
   *    ['google-order-number']['VALUE']);
   * Send deliver order: $Grequest->SendDeliverOrder($data[$root]
   *    ['google-order-number']['VALUE'], <carrier>, <tracking-number>,
   *    <send_mail>);
   * Send archive order: $Grequest->SendArchiveOrder($data[$root]
   *    ['google-order-number']['VALUE']);
   *
   */

  switch ($root) {
    case "request-received": {
      break;
    }
    case "error": {
      break;
    }
    case "diagnosis": {
      break;
    }
    case "checkout-redirect": {
      break;
    }
    case "merchant-calculation-callback": {
      break;
    }
		case "new-order-notification": {
			// secure_cart[0] => id_cart
			// secure_cart[1] => secure_key

			$gcheckout = new GCheckout();
			$secure_cart = explode('|', $data[$root]['shopping-cart']['merchant-private-data']['VALUE']);
			$cart = new Cart((int)$secure_cart[0]);
			$currency = $gcheckout->getCurrency((int)$cart->id_currency);
			unset($cart);

			$orderTotal = (float)($data[$root]['order-total']['VALUE']);
			$gcheckout->validateOrder((int)$secure_cart[0], _PS_OS_PAYMENT_, (float)$orderTotal, 
				$gcheckout->displayName, NULL, array(), NULL, false, $secure_cart[1]);
			$Gresponse->SendAck();
			break;
    }
    case "order-state-change-notification": {
      $Gresponse->SendAck();
      break;
    }
    case "charge-amount-notification": {
      $Gresponse->SendAck();
      break;
    }
    case "chargeback-amount-notification": {
      $Gresponse->SendAck();
      break;
    }
    case "refund-amount-notification": {
      $Gresponse->SendAck();
      break;
    }
    case "risk-information-notification": {
      $Gresponse->SendAck();
      break;
    }
    default:
      $Gresponse->SendBadRequestStatus("Invalid or not supported Message");
      break;
  }
  
  
  /* In case the XML API contains multiple open tags
     with the same value, then invoke this function and
     perform a foreach on the resultant array.
     This takes care of cases when there is only one unique tag
     or multiple tags.
     Examples of this are "anonymous-address", "merchant-code-string"
     from the merchant-calculations-callback API
  */
  function get_arr_result($child_node) {
    $result = array();
    if(isset($child_node)) {
      if(is_associative_array($child_node)) {
        $result[] = $child_node;
      }
      else {
        foreach($child_node as $curr_node){
          $result[] = $curr_node;
        }
      }
    }
    return $result;
  }

  /* Returns true if a given variable represents an associative array */
  function is_associative_array( $var ) {
    return is_array( $var ) && !is_numeric( implode( '', array_keys( $var ) ) );
  }


