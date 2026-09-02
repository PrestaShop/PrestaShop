<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Exception;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Command\BulkDeleteProductsFromOrderReturnCommand;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Command\DeleteProductFromOrderReturnCommand;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Command\UpdateOrderReturnStateCommand;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\OrderReturnConstraintException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Query\GetOrderReturnForEditing;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Query\GetOrderReturnProducts;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\QueryResult\OrderReturnProductForEditing;
use PrestaShopDatabaseException;
use PrestaShopException;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;

class OrderReturnFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @When I change order return :orderReturnReference state to :orderReturnStateReference
     *
     * @throws OrderReturnConstraintException
     */
    public function updateOrderReturnState(string $orderReturnReference, string $orderReturnStateReference): void
    {
        $orderReturnId = $this->getSharedStorage()->get($orderReturnReference);
        $orderReturnStateId = $this->getSharedStorage()->get($orderReturnStateReference);

        $this->getCommandBus()->handle(
            new UpdateOrderReturnStateCommand(
                (int) $orderReturnId,
                (int) $orderReturnStateId
            )
        );
    }

    /**
     * @Given :orderReturnReference has state :orderReturnStateReference
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function checkOrderReturnState(string $orderReturnReference, string $orderReturnStateReference): void
    {
        $orderReturnId = SharedStorage::getStorage()->get($orderReturnReference);

        $orderReturn = $this->getCommandBus()->handle(new GetOrderReturnForEditing((int) $orderReturnId));
        $orderReturnStateId = SharedStorage::getStorage()->get($orderReturnStateReference);
        if ($orderReturn->getOrderReturnStateId() !== $orderReturnStateId) {
            $errorMessage = sprintf('Invalid order state for  %s, expected %s but got %s', $orderReturnReference, $orderReturnStateId, $orderReturn->getOrderReturnStateId());
            throw new RuntimeException($errorMessage);
        }
    }

    /**
     * @When I query the products of order return :orderReturnReference
     */
    public function queryProductsOfOrderReturn(string $orderReturnReference): void
    {
        $orderReturnId = (int) $this->getSharedStorage()->get($orderReturnReference);
        $products = $this->getCommandBus()->handle(new GetOrderReturnProducts($orderReturnId));
        $this->getSharedStorage()->set($this->productsStorageKey($orderReturnReference), $products);
    }

    /**
     * @Then the queried products of order return :orderReturnReference should contain :count row(s)
     */
    public function assertQueriedProductsCount(string $orderReturnReference, int $count): void
    {
        $products = $this->getStoredProducts($orderReturnReference);
        if (count($products) !== $count) {
            throw new RuntimeException(sprintf(
                'Expected %d products for order return "%s", got %d.',
                $count,
                $orderReturnReference,
                count($products)
            ));
        }
    }

    /**
     * @Then the queried products of order return :orderReturnReference should contain a product with reference :reference and quantity :quantity
     */
    public function assertQueriedProductContainsReferenceWithQuantity(
        string $orderReturnReference,
        string $reference,
        int $quantity
    ): void {
        foreach ($this->getStoredProducts($orderReturnReference) as $product) {
            if ($product->getReference() === $reference && $product->getQuantity() === $quantity) {
                return;
            }
        }

        throw new RuntimeException(sprintf(
            'No product row with reference "%s" and quantity %d found for order return "%s".',
            $reference,
            $quantity,
            $orderReturnReference
        ));
    }

    /**
     * @When I delete from order return :orderReturnReference the product with reference :reference
     */
    public function deleteProductFromOrderReturn(string $orderReturnReference, string $reference): void
    {
        $orderReturnId = (int) $this->getSharedStorage()->get($orderReturnReference);
        $product = $this->lookupProduct($orderReturnId, $reference);

        $this->getCommandBus()->handle(new DeleteProductFromOrderReturnCommand(
            $orderReturnId,
            $product->getOrderDetailId(),
            $product->getCustomizationId()
        ));
    }

    /**
     * @When I try to delete from order return :orderReturnReference the product with reference :reference
     */
    public function tryDeleteProductFromOrderReturn(string $orderReturnReference, string $reference): void
    {
        try {
            $this->deleteProductFromOrderReturn($orderReturnReference, $reference);
        } catch (Exception $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I stage for deletion in order return :orderReturnReference the product with reference :reference
     */
    public function stageProductDeletion(string $orderReturnReference, string $reference): void
    {
        $orderReturnId = (int) $this->getSharedStorage()->get($orderReturnReference);
        $product = $this->lookupProduct($orderReturnId, $reference);

        $key = $this->stagedStorageKey($orderReturnReference);
        $staged = $this->getSharedStorage()->exists($key) ? $this->getSharedStorage()->get($key) : [];
        $staged[] = [
            'order_detail_id' => $product->getOrderDetailId(),
            'customization_id' => $product->getCustomizationId(),
        ];
        $this->getSharedStorage()->set($key, $staged);
    }

    /**
     * @When I commit staged deletions on order return :orderReturnReference
     */
    public function commitStagedDeletions(string $orderReturnReference): void
    {
        $orderReturnId = (int) $this->getSharedStorage()->get($orderReturnReference);
        $key = $this->stagedStorageKey($orderReturnReference);
        $staged = $this->getSharedStorage()->exists($key) ? $this->getSharedStorage()->get($key) : [];

        $this->getCommandBus()->handle(new BulkDeleteProductsFromOrderReturnCommand($orderReturnId, $staged));
        $this->getSharedStorage()->clear($key);
    }

    /**
     * @When I try to commit staged deletions on order return :orderReturnReference
     */
    public function tryCommitStagedDeletions(string $orderReturnReference): void
    {
        try {
            $this->commitStagedDeletions($orderReturnReference);
        } catch (Exception $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then I should get a :exceptionShortName
     */
    public function assertLastErrorIsByShortName(string $exceptionShortName): void
    {
        $fqcn = 'PrestaShop\\PrestaShop\\Core\\Domain\\OrderReturn\\Exception\\' . $exceptionShortName;
        if (!class_exists($fqcn)) {
            throw new RuntimeException(sprintf('Unknown OrderReturn exception class "%s".', $fqcn));
        }
        $this->assertLastErrorIs($fqcn);
    }

    /**
     * @return OrderReturnProductForEditing[]
     */
    private function getStoredProducts(string $orderReturnReference): array
    {
        $key = $this->productsStorageKey($orderReturnReference);
        if (!$this->getSharedStorage()->exists($key)) {
            throw new RuntimeException(sprintf(
                'No queried products stored for order return "%s" — call the "I query the products of ..." step first.',
                $orderReturnReference
            ));
        }

        return $this->getSharedStorage()->get($key);
    }

    private function lookupProduct(int $orderReturnId, string $reference): OrderReturnProductForEditing
    {
        $products = $this->getCommandBus()->handle(new GetOrderReturnProducts($orderReturnId));
        foreach ($products as $product) {
            if ($product->getReference() === $reference) {
                return $product;
            }
        }

        throw new RuntimeException(sprintf(
            'No product row with reference "%s" found inside order return id %d.',
            $reference,
            $orderReturnId
        ));
    }

    private function productsStorageKey(string $orderReturnReference): string
    {
        return 'order_return_products_' . $orderReturnReference;
    }

    private function stagedStorageKey(string $orderReturnReference): string
    {
        return 'order_return_staged_deletions_' . $orderReturnReference;
    }
}
