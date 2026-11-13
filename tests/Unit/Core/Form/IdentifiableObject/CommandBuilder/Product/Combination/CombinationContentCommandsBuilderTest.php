<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Form\IdentifiableObject\CommandBuilder\Product\Combination;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Content\Command\UpdateCombinationContentCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\Combination\CombinationContentCommandsBuilder;

class CombinationContentCommandsBuilderTest extends TestCase
{
    private const COMBINATION_ID = 42;
    private const SHOP_ID = 2;
    private const MODIFY_ALL_SHOPS_PREFIX = 'modify_all_shops_';

    /**
     * @dataProvider getExpectedCommands
     */
    public function testBuildCommands(array $formData, array $expectedCommands): void
    {
        $builder = new CombinationContentCommandsBuilder(self::MODIFY_ALL_SHOPS_PREFIX);

        $this->assertEquals(
            $expectedCommands,
            $builder->buildCommands(new CombinationId(self::COMBINATION_ID), $formData, ShopConstraint::shop(self::SHOP_ID))
        );
    }

    public static function getExpectedCommands(): iterable
    {
        yield 'no content' => [
            ['header' => ['is_default' => true]],
            [],
        ];

        $localizedValues = [1 => 'english', 2 => 'français'];
        yield 'single shop' => [
            [
                'content' => [
                    'description' => $localizedValues,
                    'description_short' => $localizedValues,
                    'meta_description' => $localizedValues,
                    'meta_title' => $localizedValues,
                ],
            ],
            [
                (new UpdateCombinationContentCommand(self::COMBINATION_ID, ShopConstraint::shop(self::SHOP_ID)))
                    ->setLocalizedDescriptions($localizedValues)
                    ->setLocalizedShortDescriptions($localizedValues)
                    ->setLocalizedMetaDescriptions($localizedValues)
                    ->setLocalizedMetaTitles($localizedValues),
            ],
        ];

        yield 'meta title for all shops' => [
            [
                'content' => [
                    'description' => $localizedValues,
                    'meta_title' => $localizedValues,
                    self::MODIFY_ALL_SHOPS_PREFIX . 'meta_title' => true,
                ],
            ],
            [
                (new UpdateCombinationContentCommand(self::COMBINATION_ID, ShopConstraint::shop(self::SHOP_ID)))
                    ->setLocalizedDescriptions($localizedValues),
                (new UpdateCombinationContentCommand(self::COMBINATION_ID, ShopConstraint::allShops()))
                    ->setLocalizedMetaTitles($localizedValues),
            ],
        ];
    }
}
