<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Unit\Adapter\Security;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\Security\SecureModeChecker;
use PrestaShop\PrestaShop\Adapter\Tools;

class SecureModeCheckerTest extends TestCase
{
    private $secureModeChecker;

    public function setUp()
    {
        $this->secureModeChecker = new SecureModeChecker(
            $this->getMockBuilder(Configuration::class)->getMock(),
            $this->getMockBuilder(Tools::class)->getMock()
        );
    }

    /**
     * @dataProvider urlsProvider
     */
    public function testSecureUrl($subject, $expected)
    {
        $this->assertEquals($expected, $this->secureModeChecker->secureUrl($subject));
    }

    public function urlsProvider()
    {
        return [
            [
                'http://www.prestashop.com/https/http/',
                'https://www.prestashop.com/https/http/',
            ],
            [
                'https://www.prestashop.com/',
                'https://www.prestashop.com/',
            ],
            [
                'www.prestashop.com/',
                'https://www.prestashop.com/',
            ],
            [
                'www.prestashop.com/?back=https://www.prestashop.com',
                'https://www.prestashop.com/?back=https://www.prestashop.com',
            ],
            [
                'http://www.prestashop.com/?back=https%3A//www.prestashop.com',
                'https://www.prestashop.com/?back=https%3A//www.prestashop.com',
            ],
        ];
    }
}
