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
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\InvalidProductTypeException;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Command\RemoveAllAssociatedProductSuppliersCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Command\SetProductDefaultSupplierCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Command\SetProductSuppliersCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Exception\DefaultProductSupplierNotAssociatedException;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Exception\ProductSupplierException;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Query\GetProductSupplierOptions;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\QueryResult\ProductSupplierOptions;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\ValueObject\ProductSupplierId;
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
        $this->cleanLastException();
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
        $this->cleanLastException();
        try {
            $command = new SetProductDefaultSupplierCommand(
                $this->getSharedStorage()->get($productReference),
                $this->getSharedStorage()->get($defaultSupplierReference)
            );

            $this->getCommandBus()->handle($command);
        } catch (DefaultProductSupplierNotAssociatedException | InvalidProductTypeException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I set product :productReference suppliers:
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

        $this->cleanLastException();
        try {
            $command = new SetProductSuppliersCommand(
                $this->getSharedStorage()->get($productReference),
                $productSuppliers
            );

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
        } catch (DefaultProductSupplierNotAssociatedException | InvalidProductTypeException $e) {
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

        foreach ($expectedProductSuppliers as &$expectedProductSupplier) {
            $expectedProductSupplier['combination'] = CombinationId::NO_COMBINATION;
            $expectedProductSupplier['price tax excluded'] = new DecimalNumber($expectedProductSupplier['price tax excluded']);
        }

        $actualProductSuppliers = [];
        foreach ($actualProductSupplierOptions->getSuppliersInfo() as $actualProductSupplierOption) {
            $productSupplierForEditing = $actualProductSupplierOption->getProductSupplierForEditing();
            $actualProductSuppliers[] = [
                'product supplier reference' => $productSupplierForEditing->getReference(),
                'currency' => Currency::getIsoCodeById($productSupplierForEditing->getCurrencyId()),
                'price tax excluded' => new DecimalNumber($productSupplierForEditing->getPriceTaxExcluded()),
                'combination' => $productSupplierForEditing->getCombinationId(),
            ];
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
            $this->getProductSupplierOptions($productReference)->getSuppliersInfo(),
            sprintf('Expected product %s to have no suppliers assigned', $productReference)
        );
    }

    /**
     * @Then I should get error that supplier is not associated with product
     */
    public function assertFailedUpdateDefaultSupplierWhichIsNotAssigned(): void
    {
        $this->assertLastErrorIs(DefaultProductSupplierNotAssociatedException::class);
    }

    /**
     * @Then product :productReference should not have a default supplier
     *
     * @param string $productReference
     */
    public function assertProductHasNoDefaultSupplier(string $productReference): void
    {
        $defaultSupplierId = $this->getProductSupplierOptions($productReference)->getDefaultSupplierId();

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
        $productSupplierOptions = $this->getProductSupplierOptions($productReference);

        if (isset($data['default supplier'])) {
            Assert::assertEquals(
                $this->getSharedStorage()->get($data['default supplier']),
                $productSupplierOptions->getDefaultSupplierId(),
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
