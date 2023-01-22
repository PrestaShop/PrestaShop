<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
