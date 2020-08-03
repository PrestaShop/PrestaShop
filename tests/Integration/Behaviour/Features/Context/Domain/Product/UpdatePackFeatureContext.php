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
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductPackCommand;
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
        $data = $table->getRowsHash();

        $products = [];
        foreach ($data as $productReference => $quantity) {
            $products[] = [
                'product_id' => $this->getSharedStorage()->get($productReference),
                'quantity' => (int) $quantity,
            ];
        }

        $packId = $this->getSharedStorage()->get($packReference);

        $this->upsertPack($packId, $products);
    }

    /**
     * @When I clean pack :packReference
     *
     * @param string $packReference
     */
    public function cleanPack(string $packReference)
    {
        $packId = $this->getSharedStorage()->get($packReference);

        try {
            $this->getCommandBus()->handle(UpdateProductPackCommand::cleanPack($packId));
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
        $data = $table->getRowsHash();
        $packId = $this->getSharedStorage()->get($packReference);
        $packedProducts = $this->getQueryBus()->handle(new GetPackedProducts($packId));
        $notExistingProducts = [];

        foreach ($data as $productReference => $quantity) {
            $expectedQty = (int) $quantity;
            $expectedPackedProductId = $this->getSharedStorage()->get($productReference);
            $foundProduct = false;

            /**
             * @var int
             * @var PackedProduct $packedProduct
             */
            foreach ($packedProducts as $key => $packedProduct) {
                //@todo: check && combination id when asserting combinations.
                if ($packedProduct->getProductId() === $expectedPackedProductId) {
                    $foundProduct = true;
                    Assert::assertEquals(
                        $expectedQty,
                        $packedProduct->getQuantity(),
                        sprintf('Unexpected quantity of packed product "%s"', $productReference)
                    );

                    //unset asserted product to check if there was any excessive actual products after loops
                    unset($packedProducts[$key]);
                    break;
                }
            }

            if (!$foundProduct) {
                $notExistingProducts[$productReference] = $quantity;
            }
        }

        if (!empty($notExistingProducts)) {
            throw new RuntimeException(sprintf(
                'Failed to find following packed products: %s',
                implode(',', array_keys($notExistingProducts))
            ));
        }

        if (!empty($packedProducts)) {
            throw new RuntimeException(sprintf(
                'Following packed products were not expected: %s',
                implode(',', array_map(function ($packedProduct) {
                    return $packedProduct->name;
                }, $packedProducts))
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

    /**
     * @param int $packId
     * @param array $products
     */
    private function upsertPack(int $packId, array $products): void
    {
        try {
            $this->getCommandBus()->handle(UpdateProductPackCommand::upsertPack(
                $packId,
                $products
            ));
        } catch (ProductException $e) {
            $this->setLastException($e);
        }
    }
}
