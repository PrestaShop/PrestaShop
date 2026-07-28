<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Search\Configuration;

use PrestaShop\PrestaShop\Adapter\Search\Configuration\SearchOptionsConfiguration;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use Tests\TestCase\AbstractConfigurationTestCase;

class SearchOptionsConfigurationTest extends AbstractConfigurationTestCase
{
    private const SHOP_ID = 42;

    private const VALID_CONFIGURATION = [
        'search_within_word' => true,
        'search_exact_end_match' => false,
        'fuzzy_search' => true,
        'fuzzy_max_words' => 5,
        'fuzzy_max_difference' => 3,
        'max_word_length' => 15,
        'min_word_length' => 2,
        'blacklisted_words' => [
            1 => 'foo|bar',
            2 => 'baz|qux',
        ],
    ];

    /**
     * @dataProvider provideShopConstraints
     */
    public function testGetConfiguration(ShopConstraint $shopConstraint): void
    {
        $configuration = new SearchOptionsConfiguration(
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
                ['PS_SEARCH_START', false, $shopConstraint, true],
                ['PS_SEARCH_END', false, $shopConstraint, false],
                ['PS_SEARCH_FUZZY', false, $shopConstraint, true],
                ['PS_SEARCH_FUZZY_MAX_LOOP', 0, $shopConstraint, 5],
                ['PS_SEARCH_FUZZY_MAX_DIFFERENCE', 0, $shopConstraint, 3],
                ['PS_SEARCH_MAX_WORD_LENGTH', 0, $shopConstraint, 15],
                ['PS_SEARCH_MINWORDLEN', 0, $shopConstraint, 2],
                ['PS_SEARCH_BLACKLIST', null, $shopConstraint, [1 => 'foo|bar', 2 => 'baz|qux']],
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
        $configuration = new SearchOptionsConfiguration(
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
            [InvalidOptionsException::class, array_merge(self::VALID_CONFIGURATION, ['search_within_word' => 'not_a_boolean'])],
            [InvalidOptionsException::class, array_merge(self::VALID_CONFIGURATION, ['max_word_length' => 'not_an_int'])],
        ];
    }

    public function testSuccessfulUpdate(): void
    {
        $configuration = new SearchOptionsConfiguration(
            $this->mockConfiguration,
            $this->mockShopConfiguration,
            $this->mockMultistoreFeature
        );

        $this->mockConfiguration
            ->expects($this->exactly(8))
            ->method('set');

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
