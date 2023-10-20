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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
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
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\NoCombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\InvalidProductTypeException;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Command\RemoveAllAssociatedProductSuppliersCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Command\SetProductDefaultSupplierCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Command\SetSuppliersCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Command\UpdateProductSuppliersCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Exception\InvalidProductSupplierAssociationException;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Exception\ProductSupplierException;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Exception\ProductSupplierNotAssociatedException;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Query\GetAssociatedSuppliers;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Query\GetProductSupplierOptions;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\QueryResult\AssociatedSuppliers;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\QueryResult\ProductSupplierOptions;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\ValueObject\ProductSupplierAssociation;
use Product;

class UpdateProductSuppliersFeatureContext extends AbstractProductFeatureContext
{
    /**
     * @When I remove all associated product :productReference suppliers
     *
     * @param string $productReference
     */
    public function removeAssociatedProductSuppliers(string $productReference): void
    {
        try {
            $this->getCommandBus()->handle(new RemoveAllAssociatedProductSuppliersCommand(
                $this->getSharedStorage()->get($productReference))
            );
        } catch (ProductSupplierException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I set product :productReference default supplier to :defaultSupplierReference
     *
     * @param string $productReference
     * @param string $defaultSupplierReference
     */
    public function updateProductDefaultSupplier(string $productReference, string $defaultSupplierReference): void
    {
        try {
            $command = new SetProductDefaultSupplierCommand(
                $this->getSharedStorage()->get($productReference),
                $this->getSharedStorage()->get($defaultSupplierReference)
            );

            $this->getCommandBus()->handle($command);
        } catch (ProductSupplierNotAssociatedException|InvalidProductTypeException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I associate suppliers to product :productReference
     *
     * @param string $productReference
     * @param TableNode $tableNode
     */
    public function associateSupplier(string $productReference, TableNode $tableNode): void
    {
        $data = $tableNode->getColumnsHash();
        $supplierIds = [];
        foreach ($data as $row) {
            $supplierIds[] = $this->getSharedStorage()->get($row['supplier']);
        }

        $productSupplierAssociations = $this->getCommandBus()->handle(new SetSuppliersCommand(
            $this->getSharedStorage()->get($productReference),
            $supplierIds
        ));

        // Reorganize input data so that they are easier to access and help to assign the references
        $productSuppliersReferences = [];
        foreach ($data as $supplierRow) {
            $supplierId = (int) $this->getSharedStorage()->get($supplierRow['supplier']);

            if (!empty($supplierRow['product_supplier'])) {
                $productSuppliersReferences[$supplierId][NoCombinationId::NO_COMBINATION_ID] = $supplierRow['product_supplier'];
            } elseif (!empty($supplierRow['combination_suppliers'])) {
                $combinationReferences = explode(';', $supplierRow['combination_suppliers']);
                foreach ($combinationReferences as $combinationReference) {
                    list($combinationReference, $productSupplierReference) = explode(':', $combinationReference);
                    $combinationId = (int) $this->getSharedStorage()->get($combinationReference);
                    $productSuppliersReferences[$supplierId][$combinationId] = $productSupplierReference;
                }
            }
        }

        /** @var ProductSupplierAssociation $productSupplierAssociation */
        foreach ($productSupplierAssociations as $productSupplierAssociation) {
            if (isset($productSuppliersReferences[$productSupplierAssociation->getSupplierId()->getValue()])) {
                $referencesForSupplier = $productSuppliersReferences[$productSupplierAssociation->getSupplierId()->getValue()];

                if (isset($referencesForSupplier[$productSupplierAssociation->getCombinationId()->getValue()])) {
                    $this->getSharedStorage()->set(
                        $referencesForSupplier[$productSupplierAssociation->getCombinationId()->getValue()],
                        $productSupplierAssociation->getProductSupplierId()->getValue()
                    );
                }
            }
        }
    }

    /**
     * @When I update product :productReference suppliers:
     *
     * @param string $productReference
     * @param TableNode $tableNode
     */
    public function updateProductSuppliers(string $productReference, TableNode $tableNode): void
    {
        $data = $tableNode->getColumnsHash();
        $productSuppliers = [];

        foreach ($data as $productSupplier) {
            $productSupplierData = [
                'supplier_id' => $this->getSharedStorage()->get($productSupplier['supplier']),
                'currency_id' => (int) Currency::getIdByIsoCode($productSupplier['currency'], 0, true),
                'reference' => $productSupplier['reference'],
                'price_tax_excluded' => $productSupplier['price_tax_excluded'],
                'combination_id' => NoCombinationId::NO_COMBINATION_ID,
            ];

            // Product supplier id is optional because supplier_id, combination_id and product_id are enough to find the reference
            // but it's used to assert association is consistent We need to be able to run this command even without the product_supplier_id
            // so that the form can associate new supplier and update their content in a single POST request (the IDs can't be known before
            // they are created).
            if (isset($productSupplier['product_supplier'])) {
                $productSupplierData['product_supplier_id'] = $this->getSharedStorage()->get($productSupplier['product_supplier']);
            }

            $productSuppliers[] = $productSupplierData;
        }

        try {
            $command = new UpdateProductSuppliersCommand(
                $this->getSharedStorage()->get($productReference),
                $productSuppliers
            );

            $productSupplierAssociations = $this->getCommandBus()->handle($command);

            Assert::assertSameSize(
                $productSuppliers,
                $productSupplierAssociations,
                'Number of updated associations does not match the input number of associations'
            );
        } catch (ProductSupplierNotAssociatedException|InvalidProductTypeException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then product :productReference should have following suppliers:
     *
     * @param string $productReference
     * @param TableNode $table
     */
    public function assertProductSuppliers(string $productReference, TableNode $table): void
    {
        $expectedProductSuppliers = $table->getColumnsHash();
        $actualProductSupplierOptions = $this->getProductSupplierOptions($productReference);

        $checkProductSuppliers = false;
        foreach ($expectedProductSuppliers as &$expectedProductSupplier) {
            $expectedProductSupplier['combination'] = NoCombinationId::NO_COMBINATION_ID;
            $expectedProductSupplier['price_tax_excluded'] = new DecimalNumber($expectedProductSupplier['price_tax_excluded']);
            $expectedProductSupplier['supplier'] = $this->getSharedStorage()->get($expectedProductSupplier['supplier']);
            // Product supplier ID can be skipped (for example when testing duplicate product)
            if (isset($expectedProductSupplier['product_supplier'])) {
                $checkProductSuppliers = true;
                $expectedProductSupplier['product_supplier'] = $this->getSharedStorage()->get($expectedProductSupplier['product_supplier']);
            }
        }

        $actualProductSuppliers = [];
        foreach ($actualProductSupplierOptions->getProductSuppliers() as $productSupplierForEditing) {
            $productSupplierData = [
                'reference' => $productSupplierForEditing->getReference(),
                'currency' => Currency::getIsoCodeById($productSupplierForEditing->getCurrencyId()),
                'price_tax_excluded' => new DecimalNumber($productSupplierForEditing->getPriceTaxExcluded()),
                'combination' => $productSupplierForEditing->getCombinationId(),
                'supplier' => $productSupplierForEditing->getSupplierId(),
            ];
            if ($checkProductSuppliers) {
                $productSupplierData['product_supplier'] = $productSupplierForEditing->getProductSupplierId();
            }
            $actualProductSuppliers[] = $productSupplierData;
        }

        Assert::assertEquals(
            $expectedProductSuppliers,
            $actualProductSuppliers,
            sprintf('Product "%s" suppliers doesn\'t match', $productReference)
        );
    }

    /**
     * @Then product :productReference default supplier reference should be empty
     *
     * @param string $productReference
     */
    public function assertProductDefaultSupplierReferenceIsEmpty(string $productReference): void
    {
        $this->assertDefaultSupplierReference($productReference, '');
    }

    /**
     * @Then product :productReference should not have any suppliers assigned
     *
     * @param string $productReference
     */
    public function assertProductHasNoSuppliers(string $productReference): void
    {
        Assert::assertEmpty(
            $this->getAssociatedSuppliers($productReference)->getSupplierIds(),
            sprintf('Expected product %s to have no suppliers assigned', $productReference)
        );
    }

    /**
     * @Then product :productReference should not have suppliers infos
     *
     * @param string $productReference
     */
    public function assertProductHasNoSuppliersInfo(string $productReference): void
    {
        Assert::assertEmpty(
            $this->getProductSupplierOptions($productReference)->getProductSuppliers(),
            sprintf('Expected product %s to have no suppliers assigned', $productReference)
        );
    }

    /**
     * @Then product :productReference should have the following suppliers assigned:
     *
     * @param string $productReference
     */
    public function assertAssignedSuppliers(string $productReference, TableNode $tableNode): void
    {
        $supplierIds = $this->getAssociatedSuppliers($productReference)->getSupplierIds();
        $expectedSupplierIds = [];
        foreach ($tableNode->getRows() as $row) {
            $expectedSupplierIds[] = $this->getSharedStorage()->get($row[0]);
        }

        Assert::assertEquals(
            $expectedSupplierIds,
            $supplierIds,
            sprintf(
                'Expected product %s to have no suppliers %s but got %s instead',
                $productReference,
                implode(',', $expectedSupplierIds),
                implode(',', $supplierIds)
            )
        );
    }

    /**
     * @Then I should get error that supplier is not associated with product
     */
    public function assertFailedUpdateDefaultSupplierWhichIsNotAssigned(): void
    {
        $this->assertLastErrorIs(ProductSupplierNotAssociatedException::class);
    }

    /**
     * @Then I should get error that an invalid association has been used
     */
    public function assertInvalidProductSupplierAssociation(): void
    {
        $this->assertLastErrorIs(InvalidProductSupplierAssociationException::class);
    }

    /**
     * @Then product :productReference should not have a default supplier
     *
     * @param string $productReference
     */
    public function assertProductHasNoDefaultSupplier(string $productReference): void
    {
        $defaultSupplierId = $this->getAssociatedSuppliers($productReference)->getDefaultSupplierId();

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
        $associatedSuppliers = $this->getAssociatedSuppliers($productReference);

        if (isset($data['default supplier'])) {
            $defaultSupplierId = !empty($data['default supplier']) ? $this->getSharedStorage()->get($data['default supplier']) : 0;
            Assert::assertEquals(
                $defaultSupplierId,
                $associatedSuppliers->getDefaultSupplierId(),
                'Unexpected product default supplier'
            );
            unset($data['default supplier']);
        }

        if (isset($data['default supplier reference'])) {
            $this->assertDefaultSupplierReference($productReference, $data['default supplier reference']);
            unset($data['default supplier reference']);
        }

        Assert::assertEmpty($data, sprintf('Some provided product supplier fields haven\'t been asserted: %s', var_export($data, true)));
    }

    /**
     * @Then I should get error that this action is forbidden for this type of product
     */
    public function assertLastErrorInvalidProductType(): void
    {
        $this->assertLastErrorIs(InvalidProductTypeException::class);
    }

    /**
     * @param string $productReference
     *
     * @return AssociatedSuppliers
     */
    private function getAssociatedSuppliers(string $productReference): AssociatedSuppliers
    {
        return $this->getQueryBus()->handle(new GetAssociatedSuppliers(
            $this->getSharedStorage()->get($productReference)
        ));
    }

    /**
     * @param string $productReference
     *
     * @return ProductSupplierOptions
     */
    private function getProductSupplierOptions(string $productReference): ProductSupplierOptions
    {
        return $this->getQueryBus()->handle(new GetProductSupplierOptions(
            $this->getSharedStorage()->get($productReference)
        ));
    }

    /**
     * product->supplier_reference is deprecated and not used in domain anymore,
     * this assertion is here only to support backwards compatibility until $product->supplier_reference is completely removed
     *
     * @param string $productReference
     * @param string $expectedValue
     */
    private function assertDefaultSupplierReference(string $productReference, string $expectedValue): void
    {
        $productId = $this->getSharedStorage()->get($productReference);
        $product = new Product($productId);

        Assert::assertEquals(
            $expectedValue,
            $product->supplier_reference,
            'Unexpected product default supplier reference'
        );
    }
}
