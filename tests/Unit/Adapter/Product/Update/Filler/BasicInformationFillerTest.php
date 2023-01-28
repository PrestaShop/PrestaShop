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

namespace Tests\Unit\Adapter\Product\Update\Filler;

use PrestaShop\PrestaShop\Adapter\Product\Update\Filler\BasicInformationFiller;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductCommand;
use Product;

class BasicInformationFillerTest extends ProductFillerTestCase
{
    /**
     * @dataProvider getDataToTestUpdatablePropertiesFilling
     *
     * @param UpdateProductCommand $command
     * @param array $expectedUpdatableProperties
     * @param Product $expectedProduct
     */
    public function testFillsUpdatableProperties(
        UpdateProductCommand $command,
        array $expectedUpdatableProperties,
        Product $expectedProduct
    ): void {
        $this->fillUpdatableProperties(
            $this->getFiller(),
            $this->mockDefaultProduct(),
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
        $command = $this->getEmptyCommand();

        yield [$command, [], $this->mockDefaultProduct()];

        $command = $this
            ->getEmptyCommand()
            ->setLocalizedNames([
                self::DEFAULT_LANG_ID => 'My name',
                2 => 'Your name',
            ])
        ;
        $expectedProduct = $this->mockDefaultProduct();
        $expectedProduct->name = [
            self::DEFAULT_LANG_ID => 'My name',
            2 => 'Your name',
        ];

        yield [
            $command,
            ['name' => [self::DEFAULT_LANG_ID, 2]],
            $expectedProduct,
        ];

        $command = $this
            ->getEmptyCommand()
            ->setLocalizedNames([self::DEFAULT_LANG_ID => 'My name'])
            ->setLocalizedShortDescriptions([
                self::DEFAULT_LANG_ID => 'short desc 1',
                2 => 'short desc 2',
            ])
            ->setLocalizedDescriptions([self::DEFAULT_LANG_ID => 'desc 1'])
        ;
        $expectedProduct = $this->mockDefaultProduct();
        $expectedProduct->name = [self::DEFAULT_LANG_ID => 'My name'];
        $expectedProduct->description_short = [
            self::DEFAULT_LANG_ID => 'short desc 1',
            2 => 'short desc 2',
        ];
        $expectedProduct->description = [self::DEFAULT_LANG_ID => 'desc 1'];

        yield [
            $command,
            [
                'name' => [self::DEFAULT_LANG_ID],
                'description' => [self::DEFAULT_LANG_ID],
                'description_short' => [self::DEFAULT_LANG_ID, 2],
            ],
            $expectedProduct,
        ];
    }

    /**
     * @return BasicInformationFiller
     */
    private function getFiller(): BasicInformationFiller
    {
        return new BasicInformationFiller(
            self::DEFAULT_LANG_ID
        );
    }
}
