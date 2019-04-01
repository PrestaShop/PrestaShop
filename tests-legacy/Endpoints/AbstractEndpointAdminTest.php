<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace LegacyTests\Endpoints;

use Cache;
use PhpEncryption;

abstract class AbstractEndpointAdminTest extends AbstractEndpointTest
{
    protected function employeeLogin()
    {
        $cipherTool = new PhpEncryption(_NEW_COOKIE_KEY_);
        $cookieContent = 'id_employee|1¤';
        $cookieContent .= 'checksum|' . crc32(_COOKIE_IV_ . $cookieContent);
        $cookieName = 'PrestaShop-' . md5(_PS_VERSION_ . 'psAdmin');
        $_COOKIE[$cookieName] = $cipherTool->encrypt($cookieContent);
        Cache::store('isLoggedBack' . 1, true);
    }
}
