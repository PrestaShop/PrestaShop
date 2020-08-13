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

namespace Tests\Integration\Behaviour\Features\Context\Domain\Product;

use Behat\Gherkin\Node\TableNode;
use Currency;
use PHPUnit\Framework\Assert;
use PrestaShop\Decimal\Number;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Command\UpdateProductSuppliersCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Exception\ProductSupplierException;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\ValueObject\ProductSupplierId;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\SupplierException;

class UpdateProductSuppliersFeatureContext extends AbstractProductFeatureContext
{
    /**
     * @When I delete all product :productReference suppliers
     *
     * @param string $productReference
     */
    public function deleteAllProductSuppliers(string $productReference)
    {
        try {
            $command = new UpdateProductSuppliersCommand($this->getSharedStorage()->get($productReference));
            $command->setProductSuppliers([]);
            $this->getCommandBus()->handle($command);
        } catch (ProductException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I update product :productReference suppliers with following values:
     *
     * @param string $productReference
     * @param TableNode $tableNode
     */
    public function updateProductSuppliers(string $productReference, TableNode $tableNode): void
    {
        $data = $tableNode->getColumnsHash();
        $productSuppliers = [];
        $references = [];

        foreach ($data as $productSupplier) {
            $productSupplierId = null;
            $combinationId = CombinationId::NO_COMBINATION;
            $references[] = $productSupplier['reference'];

            if ($this->getSharedStorage()->exists($productSupplier['reference'])) {
                $productSupplierId = $this->getSharedStorage()->get($productSupplier['reference']);
            }

            if (isset($productSupplier['combination']) && $this->getSharedStorage()->exists($productSupplier['combination'])) {
                $combinationId = $this->getSharedStorage()->get($productSupplier['combination']);
            }

            $productSuppliers[] = [
                'supplier_id' => $this->getSharedStorage()->get($productSupplier['supplier reference']),
                'currency_id' => (int) Currency::getIdByIsoCode($productSupplier['currency'], 0, true),
                'reference' => $productSupplier['product supplier reference'],
                'price_tax_excluded' => $productSupplier['price tax excluded'],
                'combination_id' => $combinationId,
                'product_supplier_id' => $productSupplierId,
            ];
        }

        try {
            $command = new UpdateProductSuppliersCommand($this->getSharedStorage()->get($productReference));
            $command->setProductSuppliers($productSuppliers);

            $productSupplierIds = $this->getCommandBus()->handle($command);

            Assert::assertSameSize(
                $references,
                $productSupplierIds,
                'Cannot set references in shared storage. References and actual product suppliers doesn\'t match.'
            );

            /** @var ProductSupplierId $productSupplierId */
            foreach ($productSupplierIds as $key => $productSupplierId) {
                $this->getSharedStorage()->set($references[$key], $productSupplierId->getValue());
            }
        } catch (ProductSupplierException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I set product :productReference default supplier to :supplierReference
     *
     * @param string $productReference
     * @param string $supplierReference
     *
     * @throws SupplierException
     */
    public function updateProductDefaultSupplier(string $productReference, string $supplierReference)
    {
        try {
            $command = new UpdateProductSuppliersCommand($this->getSharedStorage()->get($productReference));
            $command->setDefaultSupplierId($this->getSharedStorage()->get($supplierReference));

            $this->getCommandBus()->handle($command);
        } catch (ProductException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then product :productReference should have following suppliers:
     *
     * @param string $productReference
     * @param TableNode $table
     */
    public function assertProductSuppliers(string $productReference, TableNode $table)
    {
        $expectedProductSuppliers = $table->getColumnsHash();
        $actualProductSupplierOptions = $this->getProductForEditing($productReference)->getProductSupplierOptions();

        foreach ($expectedProductSuppliers as &$expectedProductSupplier) {
            if (isset($expectedProductSupplier['combination'])) {
                $expectedProductSupplier['combination'] = $this->getSharedStorage()->get($expectedProductSupplier['combination']);
            } else {
                $expectedProductSupplier['combination'] = CombinationId::NO_COMBINATION;
            }
            $expectedProductSupplier['price tax excluded'] = new Number($expectedProductSupplier['price tax excluded']);
        }

        $actualProductSuppliers = [];
        foreach ($actualProductSupplierOptions->getOptionsBySupplier() as $actualProductSupplierOption) {
            foreach ($actualProductSupplierOption->getProductSuppliersForEditing() as $productSupplierForEditing) {
                $actualProductSuppliers[] = [
                    'product supplier reference' => $productSupplierForEditing->getReference(),
                    'currency' => Currency::getIsoCodeById($productSupplierForEditing->getCurrencyId()),
                    'price tax excluded' => new Number($productSupplierForEditing->getPriceTaxExcluded()),
                    'combination' => $productSupplierForEditing->getCombinationId(),
                ];
            }
        }

        Assert::assertEquals(
            $expectedProductSuppliers,
            $actualProductSuppliers,
            sprintf('Product "%s" suppliers doesn\'t match', $productReference)
        );
    }

    /**
     * @Then product :productReference should not have any suppliers assigned
     *
     * @param string $productReference
     */
    public function assertProductHasNoSuppliers(string $productReference)
    {
        $productForEditing = $this->getProductForEditing($productReference);

        Assert::assertEmpty(
            $productForEditing->getProductSupplierOptions()->getOptionsBySupplier(),
            sprintf('Expected product %s to have no suppliers assigned', $productReference)
        );
    }

    /**
     * @Then product :productReference default supplier reference should be empty
     *
     * @param string $productReference
     */
    public function assertProductDefaultSupplierReferenceIsEmpty(string $productReference)
    {
        $productForEditing = $this->getProductForEditing($productReference);

        Assert::assertEmpty(
            $productForEditing->getProductSupplierOptions()->getDefaultSupplierReference(),
            sprintf('Expected product "%s" default supplier reference to be empty', $productReference)
        );
    }

    /**
     * @Then I should get error that I cannot update default supplier
     */
    public function assertFailedUpdateDefaultSupplierWhichIsNotAssigned()
    {
        $this->assertLastErrorIs(
            CannotUpdateProductException::class,
            CannotUpdateProductException::FAILED_UPDATE_DEFAULT_SUPPLIER
        );
    }

    /**
     * @Then product :productReference should not have a default supplier
     *
     * @param string $productReference
     */
    public function assertProductHasNoDefaultSupplier(string $productReference)
    {
        $productForEditing = $this->getProductForEditing($productReference);
        $defaultSupplierId = $productForEditing->getProductSupplierOptions()->getDefaultSupplierId();

        Assert::assertEmpty(
            $defaultSupplierId,
            sprintf('Product "%s" expected to have no default supplier', $productReference)
        );
    }

    /**
     * @Then product :productReference should have following supplier values:
     *
     * @param string $productReference
     * @param TableNode $tableNode
     */
    public function assertDefaultSupplier(string $productReference, TableNode $tableNode): void
    {
        $data = $tableNode->getRowsHash();
        $productSupplierOptions = $this->getProductForEditing($productReference)->getProductSupplierOptions();

        if (isset($data['default supplier'])) {
            Assert::assertEquals(
                $this->getSharedStorage()->get($data['default supplier']),
                $productSupplierOptions->getDefaultSupplierId(),
                'Unexpected product default supplier'
            );
            unset($data['default supplier']);
        }

        if (isset($data['default supplier reference'])) {
            Assert::assertEquals(
                $data['default supplier reference'],
                $productSupplierOptions->getDefaultSupplierReference(),
                'Unexpected product default supplier reference'
            );
            unset($data['default supplier reference']);
        }

        Assert::assertEmpty($data, sprintf('Some provided product supplier fields haven\'t been asserted: %s', implode(',', $data)));
    }
}
