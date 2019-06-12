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

namespace Tests\Unit\PrestaShopBundle\Service\Routing;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Service\Routing\Router;

class RouterTest extends TestCase
{
    /**
     * @dataProvider urlsForTokenizationProvider
     */
    public function testTokenizeUrl($url, $expectedResult, $token)
    {
        $this->assertEquals(
            $expectedResult,
            Router::generateTokenizedUrl($url, $token)
        );
    }

    public function urlsForTokenizationProvider()
    {
        return [
            [
                'https://www.prestashop.com/test?name=value',
                'https://www.prestashop.com/test?name=value&_token=a_random_token',
                'a_random_token',
            ],
            [
                'https://www.prestashop.com/test',
                'https://www.prestashop.com/test?_token=a_random_token',
                'a_random_token',
            ],
            [
                'https://www.prestashop.com',
                'https://www.prestashop.com?_token=a_random_token',
                'a_random_token',
            ],
            [
                'https://www.prestashop.com/',
                'https://www.prestashop.com/?_token=a_random_token',
                'a_random_token',
            ],
            [
                'https://www.prestashop.com/test?arg1=value&arg2=value',
                'https://www.prestashop.com/test?arg1=value&arg2=value&_token=a_random_token',
                'a_random_token',
            ],
        ];
    }
}
