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

namespace Tests\Unit\Classes;

use PhpEncryption;
use PHPUnit\Framework\TestCase;

class PhpEncryptionTest extends TestCase
{
    const FOO = 'foo';
    private $engine;

    public function setUp()
    {
        $randomKey = PhpEncryption::createNewRandomKey();
        $this->engine = new PhpEncryption($randomKey);
    }

    public function testEncryptAndDecrypt()
    {
        $encryptedValue = $this->engine->encrypt(self::FOO);
        $this->assertSame(self::FOO, $this->engine->decrypt($encryptedValue));
    }
}
