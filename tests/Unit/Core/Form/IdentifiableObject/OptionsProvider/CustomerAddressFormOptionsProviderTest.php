<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Unit\Core\Form\IdentifiableObject\OptionsProvider;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\OptionProvider\CustomerAddressFormOptionsProvider;

class CustomerAddressFormOptionsProviderTest extends TestCase
{
    /**
     * @var CustomerAddressFormOptionsProvider
     */
    private $customerAddressFormOptionsProvider;

    protected function setUp(): void
    {
        $this->customerAddressFormOptionsProvider = new CustomerAddressFormOptionsProvider($this->mockQueryBus());
    }

    public function testGetOptions(): void
    {
        $this->assertSame(
            ['requiredFields' => ['field1', 'field2']],
            $this->customerAddressFormOptionsProvider->getOptions(1, [])
        );
    }

    public function testGetDefaultOptions(): void
    {
        $this->assertSame(
            ['requiredFields' => ['field1', 'field2']],
            $this->customerAddressFormOptionsProvider->getDefaultOptions([])
        );
    }

    /**
     * @return CommandBusInterface
     */
    private function mockQueryBus(): CommandBusInterface
    {
        $queryBus = $this
            ->getMockBuilder(CommandBusInterface::class)
            ->onlyMethods(['handle'])
            ->getMock()
        ;

        $queryBus->method('handle')
            // this data is not important, because we are not testing the queryBus in this test, but the options provider
            ->willReturn(['field1', 'field2'])
        ;

        return $queryBus;
    }
}
