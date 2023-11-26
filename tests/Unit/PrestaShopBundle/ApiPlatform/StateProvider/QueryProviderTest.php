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
use ApiPlatform\Metadata\GetCollection;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Hook\Query\GetHook;
use PrestaShop\PrestaShop\Core\Domain\Hook\Query\GetHookStatus;
use PrestaShop\PrestaShop\Core\Domain\Hook\QueryResult\Hook as HookQuery;
use PrestaShop\PrestaShop\Core\Domain\Hook\QueryResult\HookStatus;
use PrestaShop\PrestaShop\Core\Domain\Product\Query\SearchProducts;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\FoundProduct;
use PrestaShopBundle\ApiPlatform\DomainSerializer;
use PrestaShopBundle\ApiPlatform\Exception\CQRSQueryNotFoundException;
use PrestaShopBundle\ApiPlatform\Normalizer\DecimalNumberDenormalizer;
use PrestaShopBundle\ApiPlatform\Normalizer\ObjectDenormalizer;
use PrestaShopBundle\ApiPlatform\Provider\QueryProvider;
use PrestaShopBundle\ApiPlatform\Resources\FoundProduct as FoundProductDto;
use PrestaShopBundle\ApiPlatform\Resources\Hook;
use RuntimeException;

class QueryProviderTest extends TestCase
{
    /**
     * @var CommandBusInterface
     */
    private $queryBus;

    /**
     * @var DomainSerializer
     */
    private DomainSerializer $serializer;

    /**
     * Set up dependencies for HookStatusProvider
     */
    public function setUp(): void
    {
        $denormalizers = new \ArrayIterator([new DecimalNumberDenormalizer(), new ObjectDenormalizer()]);
        $this->serializer = new DomainSerializer($denormalizers);
        $this->queryBus = $this->createMock(CommandBusInterface::class);
        $this->queryBus
            ->method('handle')
            ->willReturnCallback(function ($query) {
                switch (get_class($query)) {
                    case GetHookStatus::class:
                        return $this->createHookStatusResultBasedOnQuery($query);
                    case SearchProducts::class:
                        return $this->createsSearchProductResultBasedOnQuery($query);
                    case GetHook::class:
                        return $this->createResultBasedOnHookQuery($query);
                }

                throw new RuntimeException(sprintf('Query type %s was not expected in query bus mock', get_class($query)));
            });
    }

    public function testProvideHookStatus(): void
    {
        $hookStatusProvider = new QueryProvider($this->queryBus, $this->serializer);
        $get = new Get();
        $get = $get
            ->withExtraProperties(['CQRSQuery' => GetHookStatus::class])
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
        $hookStatusProvider = new QueryProvider($this->queryBus, $this->serializer);
        $get = new Get();
        $get = $get
            ->withExtraProperties(['CQRSQuery' => GetHook::class])
            ->withClass(Hook::class);
        /** @var Hook $hook */
        $hook = $hookStatusProvider->provide($get, ['id' => 1]);
        self::assertEquals(false, $hook->active);
        self::assertEquals('testName1', $hook->name);
        self::assertEquals('testTitle1', $hook->title);
        self::assertEquals('testDescription1', $hook->description);
        /** @var Hook $hook */
        $hook = $hookStatusProvider->provide($get, ['id' => 2]);
        self::assertTrue($hook->active);
        self::assertEquals('testName2', $hook->name);
        self::assertEquals('testTitle2', $hook->title);
        self::assertEquals('testDescription2', $hook->description);
    }

    public function testSearchProduct(): void
    {
        $searchProductProvider = new QueryProvider($this->queryBus, $this->serializer);
        $get = new GetCollection();
        $get = $get
            ->withExtraProperties(['CQRSQuery' => SearchProducts::class])
            ->withClass(FoundProductDto::class);
        $searchProducts = $searchProductProvider->provide($get, ['phrase' => 'mug', 'resultsLimit' => 10, 'isoCode' => 'EUR']);
        self::assertCount(1, $searchProducts);

        $searchProductProvider = new QueryProvider($this->queryBus, $this->serializer);
        $searchProducts = $searchProductProvider->provide($get, ['phrase' => 'search with order id', 'resultsLimit' => 10, 'isoCode' => 'EUR'], ['filters' => ['orderId' => 1]]);
        self::assertCount(0, $searchProducts);
    }

    public function testProvideNoQueryThrowsException(): void
    {
        $hookStatusProvider = new QueryProvider($this->queryBus, $this->serializer);
        $get = new Get();

        $this->expectException(CQRSQueryNotFoundException::class);
        $hookStatusProvider->provide($get, ['id' => 1]);
    }

    private function createHookStatusResultBasedOnQuery(GetHookStatus $query): HookStatus
    {
        if ($query->getId()->getValue() === 1) {
            return new HookStatus($query->getId()->getValue(), false);
        }

        if ($query->getId()->getValue() === 2) {
            return new HookStatus($query->getId()->getValue(), true);
        }

        throw new RuntimeException(sprintf('Hook "%s" was not expected in query bus mock', $query->getId()->getValue()));
    }

    /**
     * @return FoundProduct[]
     */
    private function createsSearchProductResultBasedOnQuery(SearchProducts $query): array
    {
        if ($query->getPhrase() === 'mug') {
            return [
                new FoundProduct(
                    1,
                    'mug',
                    '10 €',
                    10,
                    10,
                    0,
                    10,
                    '',
                    true
                ),
            ];
        }

        if ($query->getOrderId() && ($query->getOrderId()->getValue() === 1)) {
            return [];
        }

        throw new RuntimeException(sprintf('SearchProduct "%s" was not expected in query bus mock', $query->getPhrase()));
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
                'testName2',
                'testTitle2',
                'testDescription2'
            );
        }

        throw new RuntimeException(sprintf('Hook "%s" was not expected in query bus mock', $query->getId()->getValue()));
    }
}
