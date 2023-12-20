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

namespace Core\Context;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Context\ApiClientContextBuilder;
use PrestaShopBundle\Entity\ApiAccess;
use PrestaShopBundle\Entity\Repository\ApiAccessRepository;

class ApiClientContextBuilderTest extends TestCase
{
    public function testBuild(): void
    {
        $apiAccess = $this->getApiAccessEntity();
        $builder = new ApiClientContextBuilder($this->mockRepository($apiAccess));

        $builder->setClientId('client_id');
        $apiAccessContext = $builder->build();
        $this->assertNotNull($apiAccessContext->getApiClient());
        $this->assertEquals($apiAccess->getClientId(), $apiAccessContext->getApiClient()->getClientId());
        $this->assertEquals($apiAccess->getScopes(), $apiAccessContext->getApiClient()->getScopes());
    }

    public function testBuildNoApiAccess(): void
    {
        $builder = new ApiClientContextBuilder($this->createMock(ApiAccessRepository::class));

        $apiAccessContext = $builder->build();
        $this->assertNull($apiAccessContext->getApiClient());
    }

    private function mockRepository(ApiAccess $apiAccess): ApiAccessRepository|MockObject
    {
        $repository = $this->createMock(ApiAccessRepository::class);
        $repository
            ->method('getByClientId')
            ->willReturn($apiAccess)
        ;

        return $repository;
    }

    private function getApiAccessEntity(): ApiAccess
    {
        $apiAccess = new ApiAccess();
        $apiAccess
            ->setId(42)
            ->setClientId('client_id')
            ->setClientName('client_name')
            ->setScopes(['scope1', 'scope3'])
        ;

        return $apiAccess;
    }
}
