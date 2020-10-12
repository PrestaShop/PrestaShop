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
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\RemoveAllProductsFromPackCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\SetPackProductsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductPackException;
use PrestaShop\PrestaShop\Core\Domain\Product\Query\GetPackedProducts;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\PackedProduct;
use RuntimeException;

class UpdatePackFeatureContext extends AbstractProductFeatureContext
{
    /**
     * @When I update pack :packReference with following product quantities:
     *
     * @param string $packReference
     * @param TableNode $table
     */
    public function updateProductPack(string $packReference, TableNode $table)
    {
        $data = $table->getColumnsHash();

        $products = [];
        foreach ($data as $column) {
            $products[] = [
                'product_id' => $this->getSharedStorage()->get($column['product']),
                'quantity' => (int) $column['quantity'],
                'combination_id' => isset($column['combination']) ? $this->getSharedStorage()->get($column['combination']) : 0,
            ];
        }

        $packId = $this->getSharedStorage()->get($packReference);
        try {
            $this->getCommandBus()->handle(new SetPackProductsCommand($packId, $products));
        } catch (ProductException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I remove all products from pack :packReference
     *
     * @param string $packReference
     */
    public function removeAllProductsFromPack(string $packReference)
    {
        $packId = $this->getSharedStorage()->get($packReference);

        try {
            $this->getCommandBus()->handle(new RemoveAllProductsFromPackCommand($packId));
        } catch (ProductException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then pack :packReference should contain products with following quantities:
     *
     * @param string $packReference
     * @param TableNode $table
     */
    public function assertPackContents(string $packReference, TableNode $table)
    {
        $data = $table->getColumnsHash();
        $packId = $this->getSharedStorage()->get($packReference);
        $packedProducts = $this->getQueryBus()->handle(new GetPackedProducts($packId));
        $notExistingProducts = [];

        foreach ($data as $column) {
            $productReference = $column['product'];
            $expectedQty = (int) $column['quantity'];
            $expectedPackedProductId = $this->getSharedStorage()->get($productReference);
            $expectedCombinationId = isset($column['combination']) ? $this->getSharedStorage()->get($column['combination']) : 0;
            $foundProduct = false;

            /**
             * @var int
             * @var PackedProduct $packedProduct
             */
            foreach ($packedProducts as $key => $packedProduct) {
                if ($packedProduct->getProductId() === $expectedPackedProductId) {
                    $foundProduct = true;
                    Assert::assertEquals(
                        $expectedQty,
                        $packedProduct->getQuantity(),
                        sprintf('Unexpected quantity of packed product "%s"', $productReference)
                    );

                    Assert::assertEquals(
                        $expectedCombinationId,
                        $packedProduct->getCombinationId(),
                        sprintf('Unexpected packed product "%s" combination', $productReference)
                    );

                    //unset asserted product to check if there was any excessive actual products after loops
                    unset($packedProducts[$key]);
                    break;
                }
            }

            if (!$foundProduct) {
                if ($expectedCombinationId) {
                    $notExistingProducts[$productReference][$column['combination']] = $expectedQty;
                } else {
                    $notExistingProducts[$productReference] = $expectedQty;
                }
            }
        }

        if (!empty($notExistingProducts)) {
            throw new RuntimeException(sprintf(
                'Failed to find following packed products: %s',
                var_export($notExistingProducts, true)
            ));
        }

        if (!empty($packedProducts)) {
            throw new RuntimeException(sprintf(
                'Following packed products were not expected: %s',
                var_export($packedProducts, true)
            ));
        }
    }

    /**
     * @Then I should get error that product for packing quantity is invalid
     */
    public function assertPackProductQuantityError()
    {
        $this->assertLastErrorIs(
            ProductPackException::class,
            ProductPackException::INVALID_QUANTITY
        );
    }

    /**
     * @Then I should get error that I cannot add pack into a pack
     */
    public function assertAddingPackToPackError()
    {
        $this->assertLastErrorIs(
            ProductPackException::class,
            ProductPackException::CANNOT_ADD_PACK_INTO_PACK
        );
    }
}
