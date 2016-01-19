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
 *  @author 	PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2016 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShop\PrestaShop\Adapter;

use Monolog\Logger;
use PrestaShopBundle\Service\Log\LogInterface;

class LegacyLogger implements LogInterface
{
    public function add($message, $severity = Logger::DEBUG, array $context = [])
    {
        switch ($severity) {
            case Logger::EMERGENCY:
            case Logger::ALERT:
            case Logger::CRITICAL:
                $pslevel = 4;
                break;
            case Logger::ERROR:
                $pslevel = 3;
                break;
            case Logger::WARNING:
                $pslevel = 2;
                break;
            case Logger::NOTICE:
            case Logger::INFO:
            case Logger::DEBUG:
                $pslevel = 1;
                break;
        }

        $error_code = !empty($context['error_code'])?$context['error_code']:null;
        $object_type = !empty($context['object_type'])?$context['object_type']:null;
        $object_id = !empty($context['object_id'])?$context['object_id']:null;
        $allow_duplicate = !empty($context['allow_duplicate'])?$context['allow_duplicate']:null;

        \PrestaShopLoggerCore::addLog($message, $pslevel, $error_code, $object_type, $object_id, $allow_duplicate);
    }
}
