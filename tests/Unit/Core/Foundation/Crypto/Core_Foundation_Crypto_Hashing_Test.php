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
 *  @author 	PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2015 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShop\PrestaShop\Tests\Unit\Core\Foundation\IoC;

use Exception;
use PHPUnit_Framework_TestCase;
use PrestaShop\PrestaShop\Core\Foundation\Crypto\Hashing;

// FIXME: Defining this here will break all other Unit tests using UnitTestCase class!
//define('_COOKIE_KEY_', '2349123849231-4123');

class Core_Foundation_Crypto_Hashing_Test extends PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->hashing = new Hashing();
    }

    public function test_simple_check_hash_md5()
    {
        $this->isTrue($this->hashing->checkHash("123", md5(_COOKIE_KEY_."123"), array('cookie_key' => _COOKIE_KEY_)));
        $this->isFalse($this->hashing->checkHash("23", md5(_COOKIE_KEY_."123"), array('cookie_key' => _COOKIE_KEY_)));
    }

    public function test_simple_hash()
    {
        $this->assertTrue(is_string($this->hashing->hash("123")));
    }

    public function test_upgrades_md5_hash()
    {
        $old_style_hash = md5(_COOKIE_KEY_."123");
        $success = $this->hashing->checkHash("123", $old_style_hash, array('cookie_key' => _COOKIE_KEY_));
        $this->isTrue($success);
        $this->assertTrue(is_string($success));
        $this->assertNotEquals($old_style_hash, $success);
    }

    public function test_upgrades_hashes_on_cost_change()
    {
        $hash = $this->hashing->hash('123456789');
        $success = $this->hashing->checkHash('123456789', $hash, array('cost' => 20));
        $this->isTrue($success);
        $this->assertTrue(is_string($success));
        $this->assertNotEquals($success, $hash);
    }
}
