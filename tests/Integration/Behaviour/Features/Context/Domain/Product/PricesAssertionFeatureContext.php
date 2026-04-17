<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain\Product;

use Behat\Gherkin\Node\TableNode;
use PHPUnit\Framework\Assert;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductPricesInformation;
use RuntimeException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use TaxRulesGroup;
use Tests\Integration\Behaviour\Features\Context\Domain\TaxRulesGroupFeatureContext;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

/**
 * Context for product assertions related to Prices related properties
 */
class PricesAssertionFeatureContext extends AbstractProductFeatureContext
{
    /**
     * @Then product :productReference should have following prices information for shop(s) :shopReference:
     *
     * @param string $productReference
     * @param string $shopReferences
     * @param TableNode $tableNode
     */
    public function assertPriceFieldsForShops(string $productReference, string $shopReferences, TableNode $tableNode): void
    {
        $data = $tableNode->getRowsHash();

        $shopReferences = explode(',', $shopReferences);
        foreach ($shopReferences as $shopReference) {
            $shopId = $this->getSharedStorage()->get(trim($shopReference));
            $pricesInfo = $this->getProductForEditing(
                $productReference,
                $shopId
            )->getPricesInformation();

            $this->assertPricesInfos($pricesInfo, $data, $shopReference);
        }
    }

    /**
     * @Then product :productReference should have following prices information:
     *
     * @param string $productReference
     * @param TableNode $tableNode
     */
    public function assertPriceFields(string $productReference, TableNode $tableNode): void
    {
        $data = $tableNode->getRowsHash();
        $pricesInfo = $this->getProductForEditing($productReference)->getPricesInformation();

        $this->assertPricesInfos($pricesInfo, $data);
    }

    /**
     * @param ProductPricesInformation $pricesInfo
     * @param array $data
     * @param string|null $shopReference
     */
    protected function assertPricesInfos(ProductPricesInformation $pricesInfo, array $data, ?string $shopReference = null): void
    {
        $shopErrorMessage = !empty($shopReference) ? sprintf(' for shop %s', $shopReference) : '';
        if (isset($data['on_sale'])) {
            $expectedOnSale = PrimitiveUtils::castStringBooleanIntoBoolean($data['on_sale']);
            $onSaleInWords = $expectedOnSale ? 'to be on sale' : 'not to be on sale';

            Assert::assertEquals(
                $expectedOnSale,
                $pricesInfo->isOnSale(),
                sprintf('Expected product %s%s', $onSaleInWords, $shopErrorMessage)
            );

            unset($data['on_sale']);
        }

        if (isset($data['unity'])) {
            $expectedUnity = $data['unity'];
            $actualUnity = $pricesInfo->getUnity();

            Assert::assertEquals(
                $expectedUnity,
                $actualUnity,
                sprintf('Tax rules group expected to be "%s", but got "%s"%s', $expectedUnity, $actualUnity, $shopErrorMessage)
            );

            unset($data['unity']);
        }

        $this->assertTaxRulesGroup($data, $pricesInfo, $shopErrorMessage);
        $this->assertNumberPriceFields($data, $pricesInfo, $shopErrorMessage);

        Assert::assertEmpty($data, sprintf('Some provided product price fields haven\'t been asserted%s: %s', $shopErrorMessage, var_export($data, true)));
    }

    /**
     * @param array $data
     * @param ProductPricesInformation $pricesInfo
     * @param string $shopErrorMessage
     */
    private function assertTaxRulesGroup(array &$data, ProductPricesInformation $pricesInfo, string $shopErrorMessage): void
    {
        if (!isset($data['tax rules group'])) {
            return;
        }

        $expectedName = $data['tax rules group'];

        if ('' === $expectedName) {
            $expectedId = 0;
        } else {
            $expectedId = (int) TaxRulesGroupFeatureContext::getTaxRulesGroupByName($expectedName)->id;
        }
        $actualId = $pricesInfo->getTaxRulesGroupId();

        if ($expectedId !== $actualId) {
            throw new RuntimeException(
                sprintf(
                    'Expected tax rules group "%s", but got "%s"%s',
                    $expectedName,
                    (new TaxRulesGroup($actualId))->name,
                    $shopErrorMessage
                )
            );
        }

        unset($data['tax rules group']);
    }

    /**
     * @param array $expectedPrices
     * @param ProductPricesInformation $actualPrices
     * @param string $shopErrorMessage
     */
    protected function assertNumberPriceFields(array &$expectedPrices, ProductPricesInformation $actualPrices, string $shopErrorMessage)
    {
        $numberPriceFields = [
            'price',
            'price_tax_included',
            'ecotax',
            'ecotax_tax_included',
            'wholesale_price',
            'unit_price',
            'unit_price_tax_included',
            'unit_price_ratio',
        ];

        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        foreach ($numberPriceFields as $field) {
            if (isset($expectedPrices[$field])) {
                $expectedNumber = new DecimalNumber((string) $expectedPrices[$field]);
                $actualNumber = $propertyAccessor->getValue($actualPrices, $field);

                if (!$expectedNumber->equals($actualNumber)) {
                    throw new RuntimeException(
                        sprintf('Product %s expected to be "%s", but is "%s"%s', $field, $expectedNumber, $actualNumber, $shopErrorMessage)
                    );
                }

                unset($expectedPrices[$field]);
            }
        }
    }
}
