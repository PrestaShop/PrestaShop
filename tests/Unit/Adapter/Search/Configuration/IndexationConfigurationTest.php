<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Search\Configuration;

use PrestaShop\PrestaShop\Adapter\Search\Configuration\IndexationConfiguration;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use Tests\TestCase\AbstractConfigurationTestCase;

class IndexationConfigurationTest extends AbstractConfigurationTestCase
{
    private const SHOP_ID = 42;

    private const VALID_CONFIGURATION = [
        'indexing' => true,
    ];

    /**
     * @dataProvider provideShopConstraints
     */
    public function testGetConfiguration(ShopConstraint $shopConstraint): void
    {
        $configuration = new IndexationConfiguration(
            $this->mockConfiguration,
            $this->mockShopConfiguration,
            $this->mockMultistoreFeature
        );

        $this->mockShopConfiguration
            ->method('getShopConstraint')
            ->willReturn($shopConstraint);

        $this->mockConfiguration
            ->method('get')
            ->willReturnMap([
                ['PS_SEARCH_INDEXATION', false, $shopConstraint, true],
            ]);

        $this->assertSame(self::VALID_CONFIGURATION, $configuration->getConfiguration());
    }

    /**
     * @dataProvider provideInvalidConfiguration
     *
     * @param array<string, mixed> $values
     */
    public function testUpdateConfigurationWithInvalidConfiguration(string $exception, array $values): void
    {
        $configuration = new IndexationConfiguration(
            $this->mockConfiguration,
            $this->mockShopConfiguration,
            $this->mockMultistoreFeature
        );

        $this->expectException($exception);
        $configuration->updateConfiguration($values);
    }

    /**
     * @return array<int, array{0: class-string, 1: array<string, mixed>}>
     */
    public function provideInvalidConfiguration(): array
    {
        return [
            [UndefinedOptionsException::class, ['does_not_exist' => 'does_not_exist']],
            [InvalidOptionsException::class, ['indexing' => 'not_a_boolean']],
        ];
    }

    public function testSuccessfulUpdate(): void
    {
        $configuration = new IndexationConfiguration(
            $this->mockConfiguration,
            $this->mockShopConfiguration,
            $this->mockMultistoreFeature
        );

        $this->mockConfiguration
            ->expects($this->once())
            ->method('set')
            ->with('PS_SEARCH_INDEXATION', true, $this->anything(), $this->anything());

        $this->assertSame([], $configuration->updateConfiguration(self::VALID_CONFIGURATION));
    }

    /**
     * @return array<int, array{0: ShopConstraint}>
     */
    public function provideShopConstraints(): array
    {
        return [
            [ShopConstraint::shop(self::SHOP_ID)],
            [ShopConstraint::shopGroup(self::SHOP_ID)],
            [ShopConstraint::allShops()],
        ];
    }
}
