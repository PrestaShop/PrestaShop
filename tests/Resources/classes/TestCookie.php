<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
