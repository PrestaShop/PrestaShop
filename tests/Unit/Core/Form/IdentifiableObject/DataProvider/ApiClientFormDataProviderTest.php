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
use PrestaShop\PrestaShop\Core\Domain\ApiClient\Query\GetApiClientForEditing;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\QueryResult\EditableApiClient;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\ApiClientFormDataProvider;

class ApiClientFormDataProviderTest extends TestCase
{
    public function testGetDefaultData(): void
    {
        $queryBusMock = $this->createMock(CommandBusInterface::class);
        $provider = new ApiClientFormDataProvider($queryBusMock);

        $this->assertEquals(['lifetime' => 3600], $provider->getDefaultData());
    }

    /**
     * @dataProvider provideApiAccessData
     */
    public function testGetData(EditableApiClient $apiAccess, array $expectedData): void
    {
        $queryBusMock = $this->createMock(CommandBusInterface::class);
        $queryBusMock
            ->method('handle')
            ->with($this->isInstanceOf(GetApiClientForEditing::class))
            ->willReturn($apiAccess)
        ;

        $provider = new ApiClientFormDataProvider($queryBusMock);
        $this->assertEquals($expectedData, $provider->getData(42));
    }

    public function provideApiAccessData(): iterable
    {
        yield 'simple case with basic fields' => [
            new EditableApiClient(
                42,
                'client-id',
                'client-name',
                true,
                'short description',
                ['api_client_read', 'hook_read'],
                3600,
                null,
            ),
            [
                'client_id' => 'client-id',
                'client_name' => 'client-name',
                'description' => 'short description',
                'enabled' => true,
                'scopes' => ['api_client_read', 'hook_read'],
                'lifetime' => 3600,
                'external_issuer' => null,
            ],
        ];

        yield 'simple case with external issuer' => [
            new EditableApiClient(
                42,
                'client-id',
                'client-name',
                true,
                'short description',
                ['api_client_read', 'hook_read'],
                3600,
                'http://localhost/authorization_server',
            ),
            [
                'client_id' => 'client-id',
                'client_name' => 'client-name',
                'description' => 'short description',
                'enabled' => true,
                'scopes' => ['api_client_read', 'hook_read'],
                'lifetime' => 3600,
                'external_issuer' => 'http://localhost/authorization_server',
            ],
        ];
    }
}
