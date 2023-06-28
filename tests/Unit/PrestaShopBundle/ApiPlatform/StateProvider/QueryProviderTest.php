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
use PrestaShop\PrestaShop\Core\Domain\Product\Query\SearchProducts;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\FoundProduct;
use PrestaShopBundle\ApiPlatform\Converters\StringToIntConverter;
use PrestaShopBundle\ApiPlatform\Exception\NoExtraPropertiesFoundException;
use PrestaShopBundle\ApiPlatform\Provider\QueryProvider;
use PrestaShopBundle\ApiPlatform\Resources\Hook;
use RuntimeException;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class QueryProviderTest extends TestCase
{
    /**
     * @var CommandBusInterface
     */
    private $queryBus;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * Set up dependencies for HookStatusProvider
     */
    public function setUp(): void
    {
        $this->serializer = new Serializer([new ObjectNormalizer()]);
        $this->queryBus = $this->createMock(CommandBusInterface::class);
        $this->requestStack = $this->createMock(RequestStack::class);
        $requestMock = $this
            ->getMockBuilder(Request::class)
            ->getMock();
        $requestMock->query = new InputBag();
        $this->requestStack
            ->method('getCurrentRequest')
            ->willReturn($requestMock);
        $this->queryBus
            ->method('handle')
            ->willReturnCallback(function ($query) {
                return $this->createHookStatusResultBasedOnQuery($query);
            })
            ->willReturnCallback(function ($query) {
                switch (get_class($query)) {
                    case GetHookStatus::class:
                        return $this->createHookStatusResultBasedOnQuery($query);
                    case SearchProducts::class:
                        return $this->createsSearchProductResultBasedOnQuery($query);
                }

                throw new RuntimeException(sprintf('Query type %s was not expected in query bus mock', get_class($query)));
            });
    }

    private function createHookStatusResultBasedOnQuery(GetHookStatus $query): HookStatus
    {
        if ($query->getHookId()->getValue() === 1) {
            return new HookStatus($query->getHookId()->getValue(), false);
        }

        if ($query->getHookId()->getValue() === 2) {
            return new HookStatus($query->getHookId()->getValue(), true);
        }

        throw new RuntimeException(sprintf('Hook "%s" was not expected in query bus mock', $query->getHookId()->getValue()));
    }

    public function testProvideHookStatus(): void
    {
        $hookStatusProvider = new QueryProvider($this->queryBus, $this->requestStack);
        $get = new Get();
        $get = $get->withExtraProperties(['query' => GetHookStatus::class]);
        /** @var HookStatus $hookStatus */
        $hookStatus = $hookStatusProvider->provide($get, ['hookId' => 1]);
        self::assertEquals(false, $hookStatus->isActive());
        /** @var HookStatus $hookStatus */
        $hookStatus = $hookStatusProvider->provide($get, ['hookId' => 2]);
        self::assertTrue($hookStatus->isActive());
    }

    /**
     * @return FoundProduct[]
     */
    public function createsSearchProductResultBasedOnQuery(SearchProducts $query): array
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
                )
            ];
        }

        if ($query->getOrderId() && ($query->getOrderId()->getValue() === 1)) {
            return [];
        }

        throw new RuntimeException(sprintf('SearchProduct "%s" was not expected in query bus mock', $query->getPhrase()));
    }

    public function testSearchProduct(): void
    {
        $searchProductProvider = new QueryProvider($this->queryBus, $this->requestStack);
        $get = new Get();
        $get = $get->withExtraProperties([
            'query' => SearchProducts::class,
            'paramConverters' => [
                'resultsLimit' => StringToIntConverter::class,
                'orderId' => StringToIntConverter::class,
            ],
        ]);
        $searchProducts = $searchProductProvider->provide($get, ['phrase' => 'mug', 'resultsLimit' => 10, 'isoCode' => 'EUR']);
        self::assertCount(1, $searchProducts);

        $this->requestStack->getCurrentRequest()->query = new InputBag(['orderId' => 1]);
        $searchProductProvider = new QueryProvider($this->queryBus, $this->requestStack);
        $searchProducts = $searchProductProvider->provide($get, ['phrase' => 'search with order id', 'resultsLimit' => 10, 'isoCode' => 'EUR']);
        self::assertCount(0, $searchProducts);
    }
}
