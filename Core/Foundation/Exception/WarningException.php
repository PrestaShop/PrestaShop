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

/**
 * This exception is thrown when a major error occurs, but we must allow process to continue
 * in degraded mode (with an alternative way) to allow user fix a setting problem for example.
 *
 * Example of use: In the Router, when a module causes a forbidden override action, we must
 * warn, shut down the module, and ask the user to uninstall the module (or fix it).
 */
class WarningException extends \Core_Foundation_Exception_Exception
{
    public $alternative = null;
    public $reportData = null;
    
    /**
     * @param string $message The message to show to the user on the admin interface
     * @param mixed $alternative The alternative data/setting to use when the main data/setting failed to be computed.
     * @param string $reportData Information to generate a 'report problem' link to PrestaShop.
     * @param number $code
     * @param Exception $previous Trace of the problem. Can be added in the report.
     */
    public function __construct($message, $alternative, $reportData = null, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->alternative = $alternative;
        $this->reportData = $reportData;
    }
}
