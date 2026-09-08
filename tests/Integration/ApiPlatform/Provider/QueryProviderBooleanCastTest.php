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
use PrestaShopBundle\ApiPlatform\Metadata\CQRSGet;
use PrestaShopBundle\ApiPlatform\Metadata\CQRSGetCollection;
use PrestaShopBundle\ApiPlatform\Provider\QueryProvider;
use PrestaShopBundle\ApiPlatform\Serializer\CQRSApiSerializer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\ApiPlatform\Resources\ApiTest;

class QueryProviderBooleanCastTest extends KernelTestCase
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
        // initialized even though their values are irrelevant to the casting under test.
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
     * @dataProvider getTinyIntBooleans
     */
    public function testTinyIntIsCastForASingleResource(int $tinyInt, bool $expected): void
    {
        $provider = $this->createQueryProvider(['productId' => 1, 'type' => 'standard', 'active' => $tinyInt]);

        $result = $provider->provide(new CQRSGet(
            uriTemplate: '/test/cqrs/bool_cast',
            class: ApiTest::class,
            CQRSQuery: GetRequiredFieldsForCustomer::class,
            scopes: [],
        ));

        // provide() is typed array|object, and the analyser has no PHPUnit extension to narrow it
        // from the assertion alone.
        self::assertInstanceOf(ApiTest::class, $result);
        /* @var ApiTest $result */
        $this->assertSame($expected, $result->active);
    }

    /**
     * @dataProvider getTinyIntBooleans
     */
    public function testTinyIntIsCastForACollection(int $tinyInt, bool $expected): void
    {
        $provider = $this->createQueryProvider([['productId' => 1, 'type' => 'standard', 'active' => $tinyInt]]);

        $result = $provider->provide(new CQRSGetCollection(
            uriTemplate: '/test/cqrs/bool_cast_collection',
            class: ApiTest::class,
            CQRSQuery: GetRequiredFieldsForCustomer::class,
            scopes: [],
        ));

        $this->assertSame($expected, $result[0]->active);
    }

    /**
     * A result that already carries a real boolean keeps its value (regression guard).
     */
    public function testRealBooleanIsUnchanged(): void
    {
        $provider = $this->createQueryProvider(['productId' => 1, 'type' => 'standard', 'active' => false]);

        $result = $provider->provide(new CQRSGet(
            uriTemplate: '/test/cqrs/bool_cast_real',
            class: ApiTest::class,
            CQRSQuery: GetRequiredFieldsForCustomer::class,
            scopes: [],
        ));

        self::assertInstanceOf(ApiTest::class, $result);
        /* @var ApiTest $result */
        $this->assertFalse($result->active);
    }

    public function getTinyIntBooleans(): iterable
    {
        yield 'enabled' => [1, true];
        yield 'disabled' => [0, false];
    }

    private function createQueryProvider(array $queryResult): QueryProvider
    {
        // The query bus is mocked so no handler runs: the query class only has to be instantiable,
        // and the returned value stands in for a CQRS query result made of raw database rows.
        $queryBus = $this->createMock(CommandBusInterface::class);
        $queryBus->method('handle')->willReturn($queryResult);

        return new QueryProvider($queryBus, $this->serializer, $this->contextParametersProvider);
    }
}
