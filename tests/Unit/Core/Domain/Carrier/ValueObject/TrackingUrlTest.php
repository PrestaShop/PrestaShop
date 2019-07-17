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

namespace Tests\Unit\Core\Domain\Carrier\ValueObject;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Exception\CarrierConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\TrackingUrl;

class TrackingUrlTest extends TestCase
{
    /**
     * @dataProvider getValidUrlValues
     */
    public function testItIsCreatedSuccessfullyWhenValidUrlValueIsGiven(string $validUrl)
    {
        new TrackingUrl($validUrl);
    }

    /**
     * @dataProvider getInvalidUrlValues
     */
    public function testItThrowsExceptionWhenInvalidUrlValueIsGiven(string $invalidUrl)
    {
        $this->expectException(CarrierConstraintException::class);
        $this->expectExceptionCode(CarrierConstraintException::INVALID_TRACKING_URL);

        new TrackingUrl($invalidUrl);
    }

    public function getValidUrlValues()
    {
        yield ['https://prestashop.com'];
        yield ['http://example.com/track.php?num=@'];
        yield ['https://example.com/track.php?num=12314'];
        yield ['http://example24-test.com/track.php?num=12314'];
    }

    public function getInvalidUrlValues()
    {
        yield ['prestashop.com'];
        yield ['/test.eu'];
        yield ['www.test.eu'];
        yield ['https://'];
        yield ['https:hello-world.com'];
        yield ['/my-carrier'];
    }
}
