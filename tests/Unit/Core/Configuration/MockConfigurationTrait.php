<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Configuration;

use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/**
 * Trait to easily mock configuration with specified configuration values passed via an array.
 * This trait must be used on a class that extends TestCase so that it can use the mock functions.
 */
trait MockConfigurationTrait
{
    protected function mockConfiguration(array $configurationValues = [], ?ShopConstraint $expectedShopConstraint = null, ?string $configurationClass = null): ShopConfigurationInterface|MockObject
    {
        $configuration = $this->createMock($configurationClass ?? ShopConfigurationInterface::class);

        if (!empty($configurationValues)) {
            $configuration
                ->method('get')
                ->will($this->returnCallback(function ($configurationName, $default, $shopConstraint) use ($configurationValues, $expectedShopConstraint) {
                    if (null !== $expectedShopConstraint) {
                        self::assertEquals($shopConstraint, $expectedShopConstraint);
                    }

                    if (isset($configurationValues[$configurationName])) {
                        return $configurationValues[$configurationName];
                    }

                    throw new InvalidArgumentException('Unhandled configuration ' . $configurationName);
                }))
            ;
        }

        return $configuration;
    }
}
