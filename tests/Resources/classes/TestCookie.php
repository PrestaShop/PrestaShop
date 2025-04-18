<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Resources\classes;

use Cookie;
use PhpEncryption;
use Tools;

/**
 * This is a degraded version of the Cookie used for tests because the legacy one performs many things internally and automatically
 * It's also hard to mock because it heavily overrides __get and __set so trying to mock these methods results in unexpected side effects
 * as Mock objects also rely on these methods so when we need a basic DTO object that mimic the legacy Cookie this class is convenient.
 */
class TestCookie extends Cookie
{
    public function __construct()
    {
        $this->_content = [];
        $this->_standalone = true;
        $this->_expire = time() + 1728000;
        $this->_path = '';
        $this->_domain = '';
        $this->_sameSite = true;
        $this->_name = 'PrestaShop-' . md5(($this->_standalone ? '' : _PS_VERSION_) . 'TestCookie' . $this->_domain);
        $this->_allow_writing = true;
        $this->_salt = Tools::passwdGen(32);
        $this->cipherTool = new PhpEncryption(_NEW_COOKIE_KEY_);
        $this->_secure = false;

        $this->update();
    }

    /**
     * Do nothing to make sure
     */
    public function write()
    {
        return;
    }
}
