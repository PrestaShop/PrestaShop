<?php
/**
 * 2007-2015 PrestaShop
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
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShop\PrestaShop\Core\Foundation\Exception;

use PrestaShop\PrestaShop\Core\Foundation\Dispatcher\EventDispatcher;
use PrestaShop\PrestaShop\Core\Foundation\Dispatcher\BaseEvent;

/**
 * This exception is thrown when a major error occurs, and we must stop the process.
 * Constructor will trigger an event in the 'message' EventDispatcher, with 'error_message' label.
 *
 * Example of use: In an action when an important action failed, and you should avoid the process to continue.
 */
class ErrorException extends \Core_Foundation_Exception_Exception
{
    /**
     * Constructor.
     *
     * @param string $message The message to show to the user on the admin interface
     * @param string $reportData Information to generate a 'report problem' link to PrestaShop.
     * @param number $code
     * @param Exception $previous Trace of the problem. Can be added in the report.
     */
    final public function __construct($message, $reportData = null, $code = 0, \Exception $previous = null, $moduleToDeactivate = null)
    {
        parent::__construct($message, $code, $previous, $reportData, $moduleToDeactivate);

        if (self::$messageDispatcher) {
            self::$messageDispatcher->dispatch('error_message', new BaseEvent($message, $this));
        }
    }
}
