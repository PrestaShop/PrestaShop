<?php
/**
 * 2007-2019 PrestaShop.
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
 * @copyright 2007-2019 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Unit\Core\Crypto;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Crypto\Hashing;

class HashingTest extends TestCase
{
    public function testSimpleCheckHashMd5()
    {
        $hashing = new Hashing();
        $salt = '2349123849231-4123';

        $this->assertTrue($hashing->checkHash('123', md5($salt . '123'), $salt));
        $this->assertFalse($hashing->checkHash('23', md5($salt . '123'), $salt));
    }

    public function testSimpleEncrypt()
    {
        $hashing = new Hashing();
        $salt = '2349123849231-4123';

        $this->assertInternalType('string', $hashing->hash('123', $salt));
    }

    public function testSimpleFirstHash()
    {
        $hashing = new Hashing();
        $salt = '2349123849231-4123';

        $this->assertTrue($hashing->isFirstHash('123', $hashing->hash('123', $salt), $salt));
        $this->assertFalse($hashing->isFirstHash('123', md5('123', $salt), $salt));
    }
}
