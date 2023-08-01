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

namespace Tests\Unit\PrestaShopBundle\ApiPlatform\StateProvider;

use ApiPlatform\Metadata\Get;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Hook\Query\GetHook;
use PrestaShop\PrestaShop\Core\Domain\Hook\Query\GetHookStatus;
use PrestaShop\PrestaShop\Core\Domain\Hook\QueryResult\Hook as HookQuery;
use PrestaShop\PrestaShop\Core\Domain\Hook\QueryResult\HookStatus;
use PrestaShopBundle\ApiPlatform\Exception\NoExtraPropertiesFoundException;
use PrestaShopBundle\ApiPlatform\Provider\QueryProvider;
use PrestaShopBundle\ApiPlatform\Resources\Hook;
use RuntimeException;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class QueryProviderTest extends TestCase
{
    /**
     * @var CommandBusInterface
     */
    private $queryBus;

    private Serializer|MockObject $serializer;

    protected function setUp(): void
    {
        $this->serializer = new Serializer([new ObjectNormalizer()]);
        $this->queryBus = $this->createMock(CommandBusInterface::class);

        $this->queryBus
            ->method('handle')
            ->willReturnCallback(function ($query) {
                switch (get_class($query)) {
                    case GetHookStatus::class:
                        return $this->createResultBasedOnHookStatusQuery($query);
                    case GetHook::class:
                        return $this->createResultBasedOnHookQuery($query);
                }

                throw new RuntimeException(sprintf('Query type %s was not expected in query bus mock', get_class($query)));
            });
    }

    private function createResultBasedOnHookStatusQuery(GetHookStatus $query): HookStatus
    {
        if ($query->getId()->getValue() === 1) {
            return new HookStatus($query->getId()->getValue(), false);
        }

        if ($query->getId()->getValue() === 2) {
            return new HookStatus($query->getId()->getValue(), true);
        }

        throw new RuntimeException(sprintf('Hook "%s" was not expected in query bus mock', $query->getId()->getValue()));
    }

    private function createResultBasedOnHookQuery(GetHook $query): HookQuery
    {
        if ($query->getId()->getValue() === 1) {
            return new HookQuery(
                $query->getId()->getValue(),
                false,
                'testName1',
                'testTitle1',
                'testDescription1'
            );
        }

        if ($query->getId()->getValue() === 2) {
            return new HookQuery(
                $query->getId()->getValue(),
                true,
                'testName1',
                'testTitle1',
                'testDescription1'
            );
        }

        throw new RuntimeException(sprintf('Hook "%s" was not expected in query bus mock', $query->getId()->getValue()));
    }

    public function testProvideHookStatus(): void
    {
        $hookStatusProvider = new QueryProvider(
            $this->queryBus,
            $this->serializer,
        );
        $get = new Get();
        $get = $get
            ->withExtraProperties(['query' => "PrestaShop\PrestaShop\Core\Domain\Hook\Query\GetHookStatus"])
            ->withClass(Hook::class);
        /** @var Hook $hookStatus */
        $hookStatus = $hookStatusProvider->provide($get, ['id' => 1]);
        self::assertEquals(false, $hookStatus->active);
        /** @var Hook $hookStatus */
        $hookStatus = $hookStatusProvider->provide($get, ['id' => 2]);
        self::assertTrue($hookStatus->active);
    }

    public function testProvideHook(): void
    {
        $hookStatusProvider = new QueryProvider(
            $this->queryBus,
            $this->serializer,
        );
        $get = new Get();
        $get = $get
            ->withExtraProperties(['query' => 'PrestaShop\PrestaShop\Core\Domain\Hook\Query\GetHook'])
            ->withClass(Hook::class);
        /** @var Hook $hookDto */
        $hookDto = $hookStatusProvider->provide($get, ['id' => 1]);
        self::assertEquals(false, $hookDto->active);
        /** @var Hook $hookDto */
        $hookDto = $hookStatusProvider->provide($get, ['id' => 2]);
        self::assertTrue($hookDto->active);
    }

    public function testProvideNoQueryThrowsException(): void
    {
        $hookStatusProvider = new QueryProvider(
            $this->queryBus,
            $this->serializer,
        );
        $get = new Get();

        $this->expectException(NoExtraPropertiesFoundException::class);
        $hookStatusProvider->provide($get, ['id' => 1]);
    }
}
