<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\ApiPlatform\Provider;

use PHPUnit\Framework\MockObject\MockObject;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Context\CurrencyContextBuilder;
use PrestaShop\PrestaShop\Core\Context\LanguageContextBuilder;
use PrestaShop\PrestaShop\Core\Context\ShopContextBuilder;
use PrestaShop\PrestaShop\Core\Domain\Customer\Query\GetRequiredFieldsForCustomer;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShopBundle\ApiPlatform\ContextParametersProvider;
use PrestaShopBundle\ApiPlatform\Metadata\CQRSGetCollection;
use PrestaShopBundle\ApiPlatform\Provider\QueryProvider;
use PrestaShopBundle\ApiPlatform\Serializer\CQRSApiSerializer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\ApiPlatform\Resources\ApiTest;

class QueryProviderCollectionTest extends KernelTestCase
{
    private CQRSApiSerializer $serializer;

    /**
     * @var ContextParametersProvider&MockObject
     */
    private ContextParametersProvider $contextParametersProvider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->serializer = self::getContainer()->get(CQRSApiSerializer::class);

        // The serializer reads the PrestaShop contexts while (de)normalizing, so they must be
        // initialized even though their values are irrelevant to the shape under test.
        /** @var ShopContextBuilder $shopContextBuilder */
        $shopContextBuilder = self::getContainer()->get('test_shop_context_builder');
        $shopContextBuilder->setShopConstraint(ShopConstraint::shop(1));
        $shopContextBuilder->setShopId(1);
        /** @var LanguageContextBuilder $languageContextBuilder */
        $languageContextBuilder = self::getContainer()->get('test_language_context_builder');
        $languageContextBuilder->setLanguageId(1);
        $languageContextBuilder->setDefaultLanguageId(1);
        /** @var CurrencyContextBuilder $currencyContextBuilder */
        $currencyContextBuilder = self::getContainer()->get('test_currency_context_builder');
        $currencyContextBuilder->setCurrencyId(1);

        $this->contextParametersProvider = $this->createMock(ContextParametersProvider::class);
        $this->contextParametersProvider->method('getContextParameters')->willReturn([]);
    }

    /**
     * A CQRS query is free to index its result by entity id - SearchCustomers does, to deduplicate
     * matches across search phrases. Those keys must not reach the response: a collection operation
     * is serialized as a JSON list, and preserved keys turn it into a JSON object keyed by id.
     */
    public function testCollectionKeyedByEntityIdIsReturnedAsAList(): void
    {
        $provider = $this->createQueryProvider([
            12 => ['productId' => 12, 'type' => 'standard'],
            7 => ['productId' => 7, 'type' => 'combinations'],
        ]);

        $result = $provider->provide($this->createCollectionOperation());

        $this->assertIsArray($result);
        $this->assertSame([0, 1], array_keys($result));
        $this->assertContainsOnlyInstancesOf(ApiTest::class, $result);
        $this->assertSame(12, $result[0]->productId);
        $this->assertSame(7, $result[1]->productId);
    }

    /**
     * A query result that is already a list keeps its order and indexes (regression guard).
     */
    public function testCollectionAlreadyAListIsUnchanged(): void
    {
        $provider = $this->createQueryProvider([
            ['productId' => 3, 'type' => 'standard'],
            ['productId' => 5, 'type' => 'pack'],
        ]);

        $result = $provider->provide($this->createCollectionOperation());

        $this->assertSame([0, 1], array_keys($result));
        $this->assertSame(3, $result[0]->productId);
        $this->assertSame('pack', $result[1]->type);
    }

    private function createCollectionOperation(): CQRSGetCollection
    {
        return new CQRSGetCollection(
            uriTemplate: '/test/cqrs/collection',
            class: ApiTest::class,
            CQRSQuery: GetRequiredFieldsForCustomer::class,
            scopes: [],
        );
    }

    private function createQueryProvider(array $queryResult): QueryProvider
    {
        // The query bus is mocked so no handler runs: the query class only has to be instantiable,
        // and the returned value stands in for the CQRS query result under test.
        $queryBus = $this->createMock(CommandBusInterface::class);
        $queryBus->method('handle')->willReturn($queryResult);

        return new QueryProvider($queryBus, $this->serializer, $this->contextParametersProvider);
    }
}
