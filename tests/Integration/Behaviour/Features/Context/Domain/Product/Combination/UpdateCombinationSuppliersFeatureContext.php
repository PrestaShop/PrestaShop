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

namespace Tests\Integration\Behaviour\Features\Context\Domain\Product\Combination;

use Behat\Gherkin\Node\TableNode;
use Currency;
use PHPUnit\Framework\Assert;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\UpdateCombinationSuppliersCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Query\GetCombinationSuppliers;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Exception\ProductSupplierNotAssociatedException;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\QueryResult\ProductSupplierForEditing;

class UpdateCombinationSuppliersFeatureContext extends AbstractCombinationFeatureContext
{
    /**
     * @When I update following suppliers for combination ":combinationReference":
     *
     * @param string $combinationReference
     * @param TableNode $table
     */
    public function updateCombinationSuppliers(string $combinationReference, TableNode $table): void
    {
        $productSuppliers = [];
        foreach ($table->getColumnsHash() as $row) {
            $productSupplierData = [
                'supplier_id' => $this->getSharedStorage()->get($row['supplier']),
                'currency_id' => (int) Currency::getIdByIsoCode($row['currency'], 0, true),
                'reference' => $row['reference'],
                'price_tax_excluded' => $row['price_tax_excluded'],
                'combination_id' => $this->getSharedStorage()->get($combinationReference),
            ];

            if (!empty($row['product_supplier'])) {
                $productSupplierData['product_supplier'] = $this->getSharedStorage()->get($row['product_supplier']);
            }

            $productSuppliers[] = $productSupplierData;
        }

        $command = new UpdateCombinationSuppliersCommand(
            $this->getSharedStorage()->get($combinationReference),
            $productSuppliers
        );

        try {
            $productSupplierAssociations = $this->getCommandBus()->handle($command);

            Assert::assertSameSize(
                $productSuppliers,
                $productSupplierAssociations,
                'Number of updated associations does not match the input number of associations'
            );
        } catch (ProductSupplierNotAssociatedException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then combination ":combinationReference" should have following suppliers:
     *
     * @param string $combinationReference
     * @param TableNode $table
     */
    public function assertSuppliers(string $combinationReference, TableNode $table): void
    {
        $expectedCombinationSuppliers = $table->getColumnsHash();
        $combinationProductSuppliers = $this->getCombinationSuppliers($combinationReference);

        $checkProductSuppliers = false;
        foreach ($expectedCombinationSuppliers as &$expectedCombinationSupplier) {
            $expectedCombinationSupplier['combination'] = $this->getSharedStorage()->get($combinationReference);
            $expectedCombinationSupplier['price_tax_excluded'] = new DecimalNumber($expectedCombinationSupplier['price_tax_excluded']);
            $expectedCombinationSupplier['supplier'] = $this->getSharedStorage()->get($expectedCombinationSupplier['supplier']);
            if (!empty($expectedCombinationSupplier['product_supplier'])) {
                $expectedCombinationSupplier['product_supplier'] = $this->getSharedStorage()->get($expectedCombinationSupplier['product_supplier']);
                $checkProductSuppliers = true;
            }
        }

        $actualCombinationSuppliers = [];
        foreach ($combinationProductSuppliers as $productSupplierForEditing) {
            $combinationSupplierData = [
                'reference' => $productSupplierForEditing->getReference(),
                'currency' => Currency::getIsoCodeById($productSupplierForEditing->getCurrencyId()),
                'price_tax_excluded' => new DecimalNumber($productSupplierForEditing->getPriceTaxExcluded()),
                'combination' => $productSupplierForEditing->getCombinationId(),
                'supplier' => $productSupplierForEditing->getSupplierId(),
            ];
            if ($checkProductSuppliers) {
                $combinationSupplierData['product_supplier'] = $productSupplierForEditing->getProductSupplierId();
            }

            $actualCombinationSuppliers[] = $combinationSupplierData;
        }

        Assert::assertEquals(
            $expectedCombinationSuppliers,
            $actualCombinationSuppliers,
            sprintf('Combination "%s" suppliers doesn\'t match', $combinationReference)
        );
    }

    /**
     * @Given combination :combinationReference should not have any suppliers assigned
     *
     * @param string $combinationReference
     */
    public function assertNoSuppliers(string $combinationReference): void
    {
        Assert::assertEmpty(
            $this->getCombinationSuppliers($combinationReference),
            sprintf('Combination "%s" should not have any suppliers assigned', $combinationReference)
        );
    }

    /**
     * @param string $combinationReference
     *
     * @return ProductSupplierForEditing[]
     */
    private function getCombinationSuppliers(string $combinationReference): array
    {
        return $this->getQueryBus()->handle(new GetCombinationSuppliers(
            $this->getSharedStorage()->get($combinationReference)
        ));
    }
}
