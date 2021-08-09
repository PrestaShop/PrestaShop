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
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\RemoveAllAssociatedCombinationSuppliersCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\SetCombinationDefaultSupplierCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\SetCombinationSuppliersCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Query\GetCombinationSuppliers;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\InvalidProductTypeException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Exception\DefaultProductSupplierNotAssociatedException;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\QueryResult\ProductSupplierInfo;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\ValueObject\ProductSupplierId;

class UpdateCombinationSuppliersFeatureContext extends AbstractCombinationFeatureContext
{
    /**
     * @When I set combination :combinationReference default supplier to :defaultSupplierReference
     *
     * @param string $combinationReference
     * @param string $defaultSupplierReference
     */
    public function updateCombinationDefaultSupplier(string $combinationReference, string $defaultSupplierReference): void
    {
        $this->cleanLastException();
        try {
            $command = new SetCombinationDefaultSupplierCommand(
                $this->getSharedStorage()->get($combinationReference),
                $this->getSharedStorage()->get($defaultSupplierReference)
            );

            $this->getCommandBus()->handle($command);
        } catch (DefaultProductSupplierNotAssociatedException | InvalidProductTypeException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I set following suppliers for combination ":combinationReference":
     *
     * @param string $combinationReference
     * @param TableNode $table
     */
    public function setCombinationSuppliers(string $combinationReference, TableNode $table): void
    {
        $references = [];
        $productSuppliers = [];
        foreach ($table->getColumnsHash() as $row) {
            $productSupplierId = null;
            $references[] = $row['reference'];
            if ($this->getSharedStorage()->exists($row['reference'])) {
                $productSupplierId = $this->getSharedStorage()->get($row['reference']);
            }

            $productSuppliers[] = [
                'supplier_id' => $this->getSharedStorage()->get($row['supplier reference']),
                'currency_id' => (int) Currency::getIdByIsoCode($row['currency'], 0, true),
                'reference' => $row['combination supplier reference'],
                'price_tax_excluded' => $row['price tax excluded'],
                'combination_id' => $this->getSharedStorage()->get($combinationReference),
                'product_supplier_id' => $productSupplierId,
            ];
        }

        $command = new SetCombinationSuppliersCommand(
            $this->getSharedStorage()->get($combinationReference),
            $productSuppliers
        );

        $productSupplierIds = $this->getCommandBus()->handle($command);

        Assert::assertSameSize(
            $references,
            $productSupplierIds,
            'Cannot set references in shared storage. References and actual combination suppliers doesn\'t match.'
        );

        /** @var ProductSupplierId $productSupplierId */
        foreach ($productSupplierIds as $key => $productSupplierId) {
            $this->getSharedStorage()->set($references[$key], $productSupplierId->getValue());
        }
    }

    /**
     * @When I remove all associated combination ":combinationReference" suppliers
     *
     * @param string $combinationReference
     */
    public function removeAssociatedCombinationSuppliers(string $combinationReference): void
    {
        try {
            $this->getCommandBus()->handle(new RemoveAllAssociatedCombinationSuppliersCommand(
                $this->getSharedStorage()->get($combinationReference))
            );
        } catch (ProductException $e) {
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
        $actualCombinationSuppliersInfo = $this->getCombinationSuppliers($combinationReference);

        foreach ($expectedCombinationSuppliers as &$expectedCombinationSupplier) {
            $expectedCombinationSupplier['combination'] = $this->getSharedStorage()->get($combinationReference);
            $expectedCombinationSupplier['price tax excluded'] = new DecimalNumber($expectedCombinationSupplier['price tax excluded']);
        }

        $actualCombinationSuppliers = [];
        foreach ($actualCombinationSuppliersInfo as $productSupplierInfo) {
            $productSupplierForEditing = $productSupplierInfo->getProductSupplierForEditing();
            $actualCombinationSuppliers[] = [
                'combination supplier reference' => $productSupplierForEditing->getReference(),
                'currency' => Currency::getIsoCodeById($productSupplierForEditing->getCurrencyId()),
                'price tax excluded' => new DecimalNumber($productSupplierForEditing->getPriceTaxExcluded()),
                'combination' => $productSupplierForEditing->getCombinationId(),
            ];
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
     * @return ProductSupplierInfo[]
     */
    private function getCombinationSuppliers(string $combinationReference): array
    {
        return $this->getQueryBus()->handle(new GetCombinationSuppliers(
            $this->getSharedStorage()->get($combinationReference)
        ));
    }
}
