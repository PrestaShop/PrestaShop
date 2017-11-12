<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShop\PrestaShop\Tests\Unit\Core\Crypto;

use PHPUnit_Framework_TestCase;
use PrestaShop\PrestaShop\Core\Crypto\Hashing;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class Core_Crypto_Hashing_Test extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (!defined('_COOKIE_KEY_')) {
            define('_COOKIE_KEY_', '2349123849231-4123');
        }
        $this->hashing = new Hashing;
    }

    public function test_simple_check_hash_md5()
    {
        $this->assertTrue($this->hashing->checkHash("123", md5(_COOKIE_KEY_."123"), _COOKIE_KEY_));
        $this->assertFalse($this->hashing->checkHash("23", md5(_COOKIE_KEY_."123"), _COOKIE_KEY_));
    }

    public function test_simple_encrypt()
    {
        $this->assertTrue(is_string($this->hashing->hash("123", _COOKIE_KEY_)));
    }

    public function test_simple_first_hash()
    {
        $this->assertTrue($this->hashing->isFirstHash("123", $this->hashing->hash("123", _COOKIE_KEY_), _COOKIE_KEY_));
        $this->assertFalse($this->hashing->isFirstHash("123", md5("123", _COOKIE_KEY_), _COOKIE_KEY_));
    }
}
