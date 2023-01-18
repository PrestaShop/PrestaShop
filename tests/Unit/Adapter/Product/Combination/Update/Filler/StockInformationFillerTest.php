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
use DateTime;
use PrestaShop\PrestaShop\Adapter\Product\Combination\Update\Filler\StockInformationFiller;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\UpdateCombinationCommand;

class StockInformationFillerTest extends CombinationFillerTestCase
{
    /**
     * @dataProvider getDataToTestUpdatablePropertiesFilling
     *
     * @param Combination $combination
     * @param UpdateCombinationCommand $command
     * @param array $expectedUpdatableProperties
     * @param Combination $expectedProduct
     */
    public function testFillsUpdatableProperties(
        Combination $combination,
        UpdateCombinationCommand $command,
        array $expectedUpdatableProperties,
        Combination $expectedProduct
    ): void {
        $this->fillUpdatableProperties(
            $this->getFiller(),
            $combination,
            $command,
            $expectedUpdatableProperties,
            $expectedProduct
        );
    }

    /**
     * @return iterable
     */
    public function getDataToTestUpdatablePropertiesFilling(): iterable
    {
        $combination = $this->mockDefaultCombination();
        $command = $this->getEmptyCommand()
            ->setMinimalQuantity(11)
        ;
        $expectedCombination = $this->mockDefaultCombination();
        $expectedCombination->minimal_quantity = 11;

        yield [
            $combination,
            $command,
            [
                'minimal_quantity',
            ],
            $expectedCombination,
        ];

        $localizedAvailableNow = [
            1 => 'français available now',
            2 => 'english available now',
        ];
        $localizedAvailableLater = [
            1 => 'français available later',
            2 => 'english available later',
        ];

        $combination = $this->mockDefaultCombination();
        $command = $this->getEmptyCommand()
            ->setLocalizedAvailableNowLabels($localizedAvailableNow)
            ->setLocalizedAvailableLaterLabels($localizedAvailableLater)
            ->setLowStockThreshold(42)
            ->setMinimalQuantity(10)
            ->setAvailableDate(new DateTime('2022-10-10'))
        ;
        $expectedCombination = $this->mockDefaultCombination();
        $expectedCombination->available_now = $localizedAvailableNow;
        $expectedCombination->available_later = $localizedAvailableLater;
        $expectedCombination->low_stock_alert = true;
        $expectedCombination->low_stock_threshold = 42;
        $expectedCombination->minimal_quantity = 10;
        $expectedCombination->available_date = '2022-10-10';

        yield [
            $combination,
            $command,
            [
                'available_later' => [1, 2],
                'available_now' => [1, 2],
                'available_date',
                'low_stock_threshold',
                'low_stock_alert',
                'minimal_quantity',
            ],
            $expectedCombination,
        ];
    }

    /**
     * @return StockInformationFiller
     */
    private function getFiller(): StockInformationFiller
    {
        return new StockInformationFiller();
    }
}
