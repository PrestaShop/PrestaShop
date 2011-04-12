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
class Tm4bSms  {

    const __TM4B_SMS_HTTP_HOST__ = 'www.tm4b.com';
    const __TM4B_SMS_HTTP_SERVICE__ = '/client/api/http.php';
    const __TM4B_SMS_HTTP_METHOD__ = 'GET';
    const __TM4B_SMS_MESSAGE_TYPE__ = 'broadcast';
    
    const __TM4B_SMS_CHECKBALANCE_TYPE__ = 'check_balance';
    const __TM4B_SMS_CHECKROUTE_TYPE__ = 'check_destination';   
    const __TM4B_SMS_CHECKSTATUS_TYPE__ = 'check_status';   

    
    // Keep the Query String for HTTP
    private $_httpQS = '';
    
    public	$msg;
    private $_to = array();

    private $errors;
    private $_user;
    private $_pass;
    private $_route;
    private $_from;

    private $_id;

    function __construct($user, $pass, $route, $from = 'tm4b', $to = array(), $message = '')
    {
        $this->msg = $message;       
		$this->_user = $user;
		$this->_pass = $pass;
		$this->_route = $route;
		$this->_from = $from;
				
       	foreach ($to as $num)
        {
          	$this->addRecipient($num);
        }
        $this->_id = array(); // identifier of the sent sms
    }


    // Build the query string for HTTP
    private function BuildQS($args)
    {
        $qs = '';
        $countArgs = 1;
        foreach ($args as $key => $value)
        {
            if (is_array($value))
            {
                $countTo = 1;
                $qs .= $key . '=';
                foreach ($value as $index => $recipient)
                {
                    if ($key == 'id')
                        $qs .= $recipient;
                    else
                        $qs .= urlencode($recipient);
                  
                    if ($countTo < sizeof($value))
                        $qs .= '|';
                    $countTo++;
                }
            }
            else
            {
                $qs .= $key . '=' . urlencode($value);
            }
         
            if ($countArgs < sizeof($args))
                $qs .= '&';
            $countArgs++;
        }
        return $qs;
    }

    // Send a HTTP Queries through sockets
    private function SendSocketHTTP()
    {
        // init infos
        if( self::__TM4B_SMS_HTTP_METHOD__ == "GET")
        {
            $script = self::__TM4B_SMS_HTTP_SERVICE__ . '?' . $this->_httpQS;
        }
        else
        {
            $script = self::__TM4B_SMS_HTTP_SERVICE__ ;
        }
      

        // Build HTTP Header
        $header  = self::__TM4B_SMS_HTTP_METHOD__ . " " . $script . " HTTP/1.1\r\n";
        $header .= "Host: " . self::__TM4B_SMS_HTTP_HOST__ . "\r\n";
        $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $header .= "Content-Length: " . Tools::strlen($this->_httpQS) . "\r\n";
        $header .= "Connection: close\r\n\r\n";
        $header .= $this->_httpQS . "\r\n";

        // Socket connection
        $socket = fsockopen( self::__TM4B_SMS_HTTP_HOST__ , 80, $errno, $errstr);
      
        if($socket) // if we're connected
        {
            fputs($socket, $header); // Send header
            while(!feof($socket))
            {
                $response[] = fgets($socket); // Grab return codes
            }
            fclose($socket);
        }
        else
        {
            $response = false;
        }
        return ($response);     
    }
 

#############################################################
#
# PUBLIC METHODS 
#
#############################################################

   // Add a recipent
   public function AddRecipient($to, $country_code = NULL)
   {
      if ($country_code)
        $to = preg_replace('/^0/', $country_code, $to);
      array_push($this->_to, $to);
   }

   // Returns the current balance of the account in credits
   public function CheckCredits()
   {
      $this->_httpQS = $this->BuildQS( array( 'username' => $this->_user,
                      'password' => $this->_pass,
                      'type' => self::__TM4B_SMS_CHECKBALANCE_TYPE__) );
	  $response = $this->SendSocketHTTP();
      if (isset($response[8]))
        return $response[8];
	  return '';
   }

   // Return the cost to send sms to a given country or number
   public function CheckRoute()
   {
      $this->_httpQS = $this->BuildQS( array ( 'username' => $this->_user,
                                             'password' => $this->_pass,
                                             'type' => self::__TM4B_SMS_CHECKROUTE_TYPE__,
                                             'dest' => $this->to,
                                             'route' => $this->_route ) );
      return  ($this->SendSocketHTTP());
   }
    
    // Send SMS trought various possible methods (SMTP / HTTP)
    public function Send($sim = 'no')
    {
     	// Return if we can't send the sms
     	if (empty($this->msg) OR empty($this->_to))
     		return false;

        $sim = ($sim == '0' ? 'no' : 'yes');

        $this->_httpQS = $this->BuildQS( array (
                                       'username' => $this->_user,
                                       'password' => $this->_pass,
                                       'type' => self::__TM4B_SMS_MESSAGE_TYPE__,
                                       'to' => $this->_to,
                                       'route' => $this->_route,
                                       'from' => $this->_from,
                                       'msg' => $this->msg,
                                       'sim' => $sim
                                       )
                                    );
	   $ret = $this->SendSocketHTTP();
       if (is_array($ret))
       {
        	$this->_id = $ret[8];
            return $ret[8];
       }
       else
             return $ret;
   }
   
   public function isSent()
   {
		if (isset($this->_id) AND preg_match('/^MT.*/', $this->_id))
			return true;
		return false;	
   }

   public function CheckStatus()
   {
      $this->_httpQS = $this->BuildQS( array (
                              $this->_user,
                               'password' => $this->_pass,
                              'type' =>  self::__TM4B_SMS_CHECKSTATUS_TYPE__,
                              'id' => $this->_id )
                     );
      echo 'QS = '.$this->_httpQS."\n";
      return $this->SendSocketHTTP();
   }
}

