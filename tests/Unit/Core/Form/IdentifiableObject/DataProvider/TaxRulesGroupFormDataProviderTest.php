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

namespace Tests\Unit\Core\Form\IdentifiableObject\DataProvider;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Query\GetTaxRulesGroupForEditing;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\QueryResult\EditableTaxRulesGroup;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\ValueObject\TaxRulesGroupId;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\TaxRulesGroupFormDataProvider;

class TaxRulesGroupFormDataProviderTest extends TestCase
{
    public function testGetDefaultData(): void
    {
        $formDataProvider = new TaxRulesGroupFormDataProvider($this->mockQueryBus());

        $this->assertEquals([], $formDataProvider->getDefaultData());
    }

    public function testGetData(): void
    {
        $formDataProvider = new TaxRulesGroupFormDataProvider($this->mockQueryBus());

        $this->assertEquals([
            'name' => 'My Tax Rules Group',
            'is_enabled' => true,
            'shop_association' => [1, 2],
        ], $formDataProvider->getData(2));
    }

    private function mockQueryBus(): CommandBusInterface
    {
        $queryBus = $this->createMock(CommandBusInterface::class);
        $queryBus
            ->method('handle')
            ->with($this->isInstanceOf(GetTaxRulesGroupForEditing::class))
            ->willReturn(
                new EditableTaxRulesGroup(
                    new TaxRulesGroupId(2),
                    'My Tax Rules Group',
                    true,
                    [1, 2]
                )
            )
        ;

        return $queryBus;
    }
}
