<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\ApiPlatform;

use PHPUnit\Framework\MockObject\MockObject;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Context\CurrencyContextBuilder;
use PrestaShop\PrestaShop\Core\Context\LanguageContextBuilder;
use PrestaShop\PrestaShop\Core\Context\ShopContextBuilder;
use PrestaShop\PrestaShop\Core\Domain\Customer\Query\GetRequiredFieldsForCustomer;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShopBundle\ApiPlatform\ContextParametersProvider;
use PrestaShopBundle\ApiPlatform\Metadata\CQRSGet;
use PrestaShopBundle\ApiPlatform\Provider\QueryProvider;
use PrestaShopBundle\ApiPlatform\Serializer\CQRSApiSerializer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\ApiPlatform\Resources\ApiTest;

class QueryProviderTest extends KernelTestCase
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

        // The serializer resolves PrestaShop context values while (de)normalizing, so the shop, language and currency
        // contexts must be initialized even though their actual values are irrelevant to the mapping under test.
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

        // The provider's own context parameters are irrelevant here: the tests only assert how the query result is
        // mapped onto the resource, so we return an empty set to keep the extra parameters (and assertions) clean.
        $this->contextParametersProvider = $this->createMock(ContextParametersProvider::class);
        $this->contextParametersProvider->method('getContextParameters')->willReturn([]);
    }

    /**
     * A CQRS query returning a plain list (e.g. a string[]) from a non-collection operation must be wrapped behind
     * the "_queryResult" key, so it can be mapped onto an array property of the API resource via CQRSQueryMapping.
     */
    public function testListQueryResultIsWrappedAndMappedOntoResourceProperty(): void
    {
        $provider = $this->createQueryProvider(['optin', 'newsletter']);
        $operation = new CQRSGet(
            class: ApiTest::class,
            CQRSQuery: GetRequiredFieldsForCustomer::class,
            CQRSQueryMapping: ['[_queryResult]' => '[names]'],
        );

        $result = $provider->provide($operation);

        $this->assertInstanceOf(ApiTest::class, $result);
        $this->assertSame(['optin', 'newsletter'], $result->names);
    }

    /**
     * A list of arrays/objects must NOT be wrapped: its elements are mapped individually through the "[@index]"
     * notation. Wrapping it under "_queryResult" would break that mapping (regression guard).
     */
    public function testListOfArraysIsNotWrapped(): void
    {
        $provider = $this->createQueryProvider([['id' => 1], ['id' => 2]]);
        $operation = new CQRSGet(
            class: ApiTest::class,
            CQRSQuery: GetRequiredFieldsForCustomer::class,
            CQRSQueryMapping: ['[@index][id]' => '[names][@index]'],
        );

        $result = $provider->provide($operation);

        $this->assertInstanceOf(ApiTest::class, $result);
        $this->assertSame([1, 2], $result->names);
    }

    private function createQueryProvider(array $queryResult): QueryProvider
    {
        // The query bus is mocked so the associated handler is never executed: the query class is only used to
        // build the query object, and the returned value stands in for the CQRS query result under test.
        $queryBus = $this->createMock(CommandBusInterface::class);
        $queryBus->method('handle')->willReturn($queryResult);

        return new QueryProvider($queryBus, $this->serializer, $this->contextParametersProvider);
    }
}
