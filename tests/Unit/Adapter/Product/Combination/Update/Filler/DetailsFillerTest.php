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
use PrestaShop\PrestaShop\Adapter\Product\Combination\Update\Filler\DetailsFiller;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\UpdateCombinationCommand;

class DetailsFillerTest extends CombinationFillerTestCase
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
        $command = $this->getEmptyCommand()
            ->setUpc('3456789')
            ->setIsbn('978-3-16-148410-1')
        ;
        $expectedCombination = $this->mockDefaultCombination();
        $expectedCombination->upc = '3456789';
        $expectedCombination->isbn = '978-3-16-148410-1';

        yield [
            $this->mockDefaultCombination(),
            $command,
            ['isbn', 'upc'],
            $expectedCombination,
        ];

        $command = $this->getEmptyCommand()
            ->setEan13('1234567890111')
            ->setIsbn('978-3-16-148410-0')
            ->setMpn('HUE222-7')
            ->setReference('ref-HUE222-7')
            ->setUpc('0123456789')
            ->setImpactOnWeight('3')
        ;
        $expectedCombination = $this->mockDefaultCombination();
        $expectedCombination->ean13 = '1234567890111';
        $expectedCombination->isbn = '978-3-16-148410-0';
        $expectedCombination->mpn = 'HUE222-7';
        $expectedCombination->reference = 'ref-HUE222-7';
        $expectedCombination->upc = '0123456789';
        $expectedCombination->weight = '3';

        yield [
            $this->mockDefaultCombination(),
            $command,
            [
                'ean13',
                'isbn',
                'mpn',
                'reference',
                'upc',
                'weight',
            ],
            $expectedCombination,
        ];
    }

    /**
     * @return DetailsFiller
     */
    private function getFiller(): DetailsFiller
    {
        return new DetailsFiller();
    }
}
