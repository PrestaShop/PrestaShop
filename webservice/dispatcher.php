<?php
/*
* 2007-2011 PrestaShop 
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
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
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

ob_start();
require_once(dirname(__FILE__).'/../config/config.inc.php');

// Use for image management (using the POST method of the browser to simulate the PUT method)
$method = isset($_REQUEST['ps_method']) ? $_REQUEST['ps_method'] : $_SERVER['REQUEST_METHOD'];

$key = isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : NULL;

if (isset($_REQUEST['xml']))
{
	// if a XML is in POST
	$input_xml = $_REQUEST['xml'];
}
else
{
	// if no XML
	$input_xml = NULL;
	
	// if a XML is in PUT
	if ($_SERVER['REQUEST_METHOD'] == 'PUT')
	{
		$putresource = fopen("php://input", "r");
		while ($putData = fread($putresource, 1024))
			$input_xml .= $putData;
		fclose($putresource);
	}
}

$params = $_GET;
unset($params['url']);

$class_name = WebserviceKey::getClassFromKey($key);
$bad_class_name = false;
if (!class_exists($class_name))
{
	$class_name = 'WebserviceRequest';
	$bad_class_name = true;
}
// fetch the request
$request = call_user_func(array($class_name, 'getInstance'));
$result = $request->fetch($key, $method, $_GET['url'], $params, $bad_class_name, $input_xml);
// display result
if (ob_get_length() == 0)
	header($result['content_type']);
else
	header('Content-Type: application/javascript'); // Useful for debug...
header($result['status']);
header($result['x_powered_by']);
header($result['execution_time']);
if (isset($result['ps_ws_version']))
	header($result['ps_ws_version']);

if ($result['type'] == 'xml')
{
	header($result['content_sha1']);
	echo $result['content'];
}
elseif ($result['type'] == 'image')
{
	if ($result['content_type'] == 'Content-Type: image/jpeg')
		imagejpeg(WebserviceRequest::getInstance()->_imageResource);
	elseif ($result['content_type'] == 'Content-Type: image/gif')
		imagegif(WebserviceRequest::getInstance()->_imageResource);
	imagedestroy(WebserviceRequest::getInstance()->_imageResource);
}

ob_end_flush();
