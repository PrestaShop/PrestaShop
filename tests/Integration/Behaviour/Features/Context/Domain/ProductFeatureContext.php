<?php

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Behat\Tester\Exception\PendingException;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\BulkDisableProductStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\BulkEnableProductStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\DeleteProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\ToggleProductStatusCommand;
use Product;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;

class ProductFeatureContext extends AbstractDomainFeatureContext
{

    /**
     * @Given /^product "([^"]*)" with id product "([^"]*)" exists$/
     */
    public function assertProductWithIdProductExists($storageReference, $productId)
    {
        $product = new Product($productId);

        if (0 >= $product->id) {
            throw new RuntimeException(
                sprintf(
                    'Product with id %s does not exist',
                    $productId
                )
            );
        }

        SharedStorage::getStorage()->set($storageReference, $product);
    }

    /**
     * @Then /^product "([^"]*)" should have status "([^"]*)"$/
     */
    public function assertProductShouldHaveStatus($storageReference, $expectedStatus)
    {
        /** @var Product $productFromStorage */
        $productFromStorage = SharedStorage::getStorage()->get($storageReference);
        $product = new Product($productFromStorage->id);

        if ((int) $product->active !== (int) $expectedStatus) {
            throw new RuntimeException(
                sprintf(
                    'Expected status %s but received %s',
                    (int) $expectedStatus,
                    (int) $product->active
                )
            );
        }
    }

    /**
     * @Then /^product with id "([^"]*)" should not exist$/
     */
    public function assertProductWithIdShouldNotExist($productId)
    {
        $product = new Product($productId);

        if ($product->id > 0) {
            throw new RuntimeException(
                sprintf('Expected product with id "%s" should not exist', $product->id)
            );
        }
    }

    /**
     * @When /^I toggle status of product "([^"]*)"$/
     */
    public function toggleStatus($storageReference)
    {
        /** @var Product $product */
        $product = SharedStorage::getStorage()->get($storageReference);
        $this->getCommandBus()->handle(new ToggleProductStatusCommand((int) $product->id));
    }

    /**
     * @When /^I bulk enable products "([^"]*)"$/
     */
    public function bulkEnableProducts($productReferences)
    {
        $storage = SharedStorage::getStorage();
        $ids = [];
        foreach (explode(',', $productReferences) as $productReference) {
            /** @var Product $productFromStorage */
            $productFromStorage = $storage->get($productReference);

            $ids[] = (int) $productFromStorage->id;
        }

        $this->getCommandBus()->handle(new BulkEnableProductStatusCommand($ids));
    }

    /**
     * @When /^I bulk disable products "([^"]*)"$/
     */
    public function bulkDisableProducts($productReferences)
    {
        $storage = SharedStorage::getStorage();
        $ids = [];
        foreach (explode(',', $productReferences) as $productReference) {
            /** @var Product $productFromStorage */
            $productFromStorage = $storage->get($productReference);

            $ids[] = (int) $productFromStorage->id;
        }

        $this->getCommandBus()->handle(new BulkDisableProductStatusCommand($ids));
    }

    /**
     * @When /^I delete product "([^"]*)"$/
     */
    public function deleteProduct($productReferences)
    {
        /** @var Product $product */
        $product = SharedStorage::getStorage()->get($productReferences);

        $this->getCommandBus()->handle(new DeleteProductCommand((int) $product->id));
    }
}
