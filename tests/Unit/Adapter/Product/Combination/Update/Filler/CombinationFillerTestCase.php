<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Unit\Adapter\Product\Combination\Update\Filler;

use Combination;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Product\Combination\Update\Filler\CombinationFillerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\UpdateCombinationCommand;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

abstract class CombinationFillerTestCase extends TestCase
{
    protected const DEFAULT_LANG_ID = 1;
    protected const DEFAULT_SHOP_ID = 2;
    protected const COMBINATION_ID = 3;

    /**
     * @param CombinationFillerInterface $filler
     * @param Combination $combination
     * @param UpdateCombinationCommand $command
     * @param array $expectedUpdatableProperties
     * @param Combination $expectedCombination
     */
    protected function fillUpdatableProperties(
        CombinationFillerInterface $filler,
        Combination $combination,
        UpdateCombinationCommand $command,
        array $expectedUpdatableProperties,
        Combination $expectedCombination
    ) {
        $this->assertSame(
            $expectedUpdatableProperties,
            $filler->fillUpdatableProperties($combination, $command)
        );

        // make sure the combination properties were filled as expected.
        $this->assertEquals($expectedCombination, $combination);
    }

    /**
     * This method mocks combination into its default state.
     * Feel free to override it if needed for specific test cases.
     *
     * @return Combination
     */
    protected function mockDefaultCombination(): Combination
    {
        $combination = $this->createMock(Combination::class);
        $combination->id = self::COMBINATION_ID;
        $combination->weight = 0;

        return $combination;
    }

    /**
     * @return UpdateCombinationCommand
     */
    protected function getEmptyCommand(): UpdateCombinationCommand
    {
        return new UpdateCombinationCommand(self::COMBINATION_ID, ShopConstraint::shop(self::DEFAULT_SHOP_ID));
    }
}
