<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Form\Admin\Sell\CustomerService\MerchandiseReturn;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShopBundle\Form\Admin\Sell\CustomerService\MerchandiseReturn\MerchandiseReturnOptionsFormDataProvider;

class MerchandiseReturnOptionsFormDataProviderTest extends TestCase
{
    public function testGetData(): void
    {
        $expectedReturn = [];

        $mockDataConfiguration = $this
            ->getMockBuilder(DataConfigurationInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockDataConfiguration
            ->expects($this->once())
            ->method('getConfiguration')
            ->willReturn($expectedReturn);

        $object = new MerchandiseReturnOptionsFormDataProvider($mockDataConfiguration);

        self::assertEquals($expectedReturn, $object->getData());
    }

    public function testSetData(): void
    {
        $expectedParam = ['enable_order_return' => true];
        $expectedReturn = [];

        $mockDataConfiguration = $this
            ->getMockBuilder(DataConfigurationInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockDataConfiguration
            ->expects($this->once())
            ->method('updateConfiguration')
            ->with($expectedParam)
            ->willReturn($expectedReturn);

        $object = new MerchandiseReturnOptionsFormDataProvider($mockDataConfiguration);

        self::assertEquals($expectedReturn, $object->setData($expectedParam));
    }
}
