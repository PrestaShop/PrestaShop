<?php

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Behat\Tester\Exception\PendingException;
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
     * @When /^I toggle status of product "([^"]*)"$/
     */
    public function toggleStatus($storageReference)
    {
        /** @var Product $currency */
        $product = SharedStorage::getStorage()->get($storageReference);
        $this->getCommandBus()->handle(new ToggleProductStatusCommand((int) $product->id));
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
}
