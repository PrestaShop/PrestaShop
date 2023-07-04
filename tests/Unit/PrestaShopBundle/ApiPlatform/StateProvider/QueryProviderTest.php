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
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Hook\Query\GetHook;
use PrestaShop\PrestaShop\Core\Domain\Hook\Query\GetHookStatus;
use PrestaShop\PrestaShop\Core\Domain\Hook\QueryResult\HookStatus;
use PrestaShopBundle\ApiPlatform\Exception\NoExtraPropertiesFoundException;
use PrestaShopBundle\ApiPlatform\Provider\QueryProvider;
use PrestaShopBundle\ApiPlatform\Resources\Hook;
use RuntimeException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Serializer;

class QueryProviderTest extends TestCase
{
    /**
     * @var CommandBusInterface
     */
    private $queryBus;

    private function createResultBasedOnQuery(GetHookStatus $query): HookStatus
    {
        if ($query->getId()->getValue() === 1) {
            return new HookStatus($query->getId()->getValue(), false);
        }

        if ($query->getId()->getValue() === 2) {
            return new HookStatus($query->getId()->getValue(), true);
        }

        throw new RuntimeException(sprintf('Hook "%s" was not expected in query bus mock', $query->getId()->getValue()));
    }

    private function createResultBasedOnObjectModel(GetHook $query): \stdClass
    {
        if ($query->getId()->getValue() === 1) {
            $objectModelHook = new \stdClass();
            $objectModelHook->active = false;

            return $objectModelHook;
        }

        if ($query->getId()->getValue() === 2) {
            $objectModelHook = new \stdClass();
            $objectModelHook->active = true;

            return $objectModelHook;
        }

        throw new RuntimeException(sprintf('Hook "%s" was not expected in query bus mock', $query->getId()->getValue()));
    }

    public function testProvideHookStatus(): void
    {
        $this->queryBus = $this->createMock(CommandBusInterface::class);
        $this->queryBus
            ->method('handle')
            ->with($this->isInstanceOf(GetHookStatus::class))
            ->willReturnCallback(function ($query) {
                return $this->createResultBasedOnQuery($query);
            })
        ;
        $hookStatusProvider = new QueryProvider(
            $this->queryBus,
            $this->createMock(DenormalizerInterface::class)
        );
        $get = new Get();
        $get = $get->withExtraProperties(['query' => "PrestaShop\PrestaShop\Core\Domain\Hook\Query\GetHookStatus"]);
        /** @var HookStatus $hookStatus */
        $hookStatus = $hookStatusProvider->provide($get, ['id' => 1]);
        self::assertEquals(false, $hookStatus->isActive());
        /** @var HookStatus $hookStatus */
        $hookStatus = $hookStatusProvider->provide($get, ['id' => 2]);
        self::assertTrue($hookStatus->isActive());
    }

    public function testProvideHook(): void
    {
        $this->queryBus = $this->createMock(CommandBusInterface::class);
        $this->queryBus
            ->method('handle')
            ->with($this->isInstanceOf(GetHook::class))
            ->willReturnCallback(function ($query) {
                return $this->createResultBasedOnObjectModel($query);
            })
        ;

        $hookStatusProvider = new QueryProvider(
            $this->queryBus,
            $this->setUpSerializerMock()
        );
        $get = new Get();
        $get = $get->withExtraProperties([
            'dto' => "PrestaShop\PrestaShop\Core\Domain\Hook\Query\GetHook",
            'denormalizer' => "src\PrestaShopBundle\ApiPlatform\Resources\Hook.php",
        ]);
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
            $this->createMock(CommandBusInterface::class),
            $this->createMock(DenormalizerInterface::class)
        );
        $get = new Get();

        $this->expectException(NoExtraPropertiesFoundException::class);
        $hookStatusProvider->provide($get, ['id' => 1]);
    }

    private function setUpSerializerMock(): Serializer
    {
        $firstHookDto = new Hook();
        $firstHookDto->active = false;

        $secondHookDto = new Hook();
        $secondHookDto->active = true;

        $serializerMock = $this->createMock(Serializer::class);
        $serializerMock
            ->expects(static::exactly(2))
            ->method('denormalize')
            ->willReturnOnConsecutiveCalls(
                $firstHookDto, $secondHookDto
            )
        ;

        return $serializerMock;
    }
}
