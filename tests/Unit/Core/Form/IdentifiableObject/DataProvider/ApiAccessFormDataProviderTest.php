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

namespace Core\Form\IdentifiableObject\DataProvider;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\ApiAccess\Query\GetApiAccessForEditing;
use PrestaShop\PrestaShop\Core\Domain\ApiAccess\QueryResult\EditableApiAccess;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\ApiAccessFormDataProvider;

class ApiAccessFormDataProviderTest extends TestCase
{
    public function testGetDefaultData(): void
    {
        $queryBusMock = $this->createMock(CommandBusInterface::class);
        $provider = new ApiAccessFormDataProvider($queryBusMock);

        $this->assertEquals([], $provider->getDefaultData());
    }

    /**
     * @dataProvider provideApiAccessData
     */
    public function testGetData(EditableApiAccess $apiAccess, array $expectedData): void
    {
        $queryBusMock = $this->createMock(CommandBusInterface::class);
        $queryBusMock
            ->method('handle')
            ->with($this->isInstanceOf(GetApiAccessForEditing::class))
            ->willReturn($apiAccess)
        ;

        $provider = new ApiAccessFormDataProvider($queryBusMock);
        $this->assertEquals($expectedData, $provider->getData(42));
    }

    public function provideApiAccessData(): iterable
    {
        yield 'simple case with basic fields' => [
            new EditableApiAccess(
                42,
                'client-id',
                'client-name',
                true,
                'short description'
            ),
            [
                'clientId' => 'client-id',
                'clientName' => 'client-name',
                'description' => 'short description',
                'enabled' => true,
            ],
        ];
    }
}
