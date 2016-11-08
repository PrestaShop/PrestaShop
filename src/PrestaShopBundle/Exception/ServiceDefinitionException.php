<?php
/**
 * 2007-2016 PrestaShop
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2016 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShopBundle\Exception;

use Exception;

/**
 * Exception thrown when a service definition failed.
 */
class ServiceDefinitionException extends Exception
{
    /**
     * @var string
     */
    public $serviceId;
    
    /**
     * 
     * @param string $message
     * @param string $serviceId
     * @param integer $code
     * @param Exception $previous
     */
    public function __construct($message = "", $serviceId, $code = 0, Exception $previous = null)
    {
        $this->serviceId = $serviceId;
        
        parent::__construct($message, $code, $previous);
    }
}
