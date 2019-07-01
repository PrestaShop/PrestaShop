<?php

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Behat\Tester\Exception\PendingException;
use Db;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\BulkDeleteProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\BulkDisableProductStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\BulkDuplicateProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\BulkEnableProductStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\DeleteProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\DuplicateProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\ToggleProductStatusCommand;
use Product;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\CommonFeatureContext;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;

class ProductFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * This is used for TYPE_HTML type for object model field - in testing context this directory is not being
     * created.
     *
     * @BeforeSuite
     */
    public static function enableHtmlPurifier($event)
    {
        $container = CommonFeatureContext::getContainer();

        $purifierCacheDirectory = _PS_CACHE_DIR_ . 'purifier';
        $filesystem = $container->get('filesystem');

        if (!$filesystem->exists($purifierCacheDirectory)) {
            $filesystem->mkdir($purifierCacheDirectory);
        }
    }

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
     * @Then /^product with reference "([^"]*)" count is equal to "([^"]*)"$/
     */
    public function assertProductWithReferenceCountIsEqualTo($productReference, $expectedCount)
    {
        $sql = '
          SELECT COUNT(p.`id_product`) FROM `' . _DB_PREFIX_ . 'product` p
          WHERE p.`reference` = "' . $productReference . '"
          ';

        $productWithReferenceCount = (int) Db::getInstance()->getValue($sql);

        if ($productWithReferenceCount !== (int) $expectedCount) {
            throw new RuntimeException(
                sprintf(
                    'Expected product with reference %s to have %s count in database but got %s',
                    $productReference,
                    $expectedCount,
                    $productWithReferenceCount
                )
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

    /**
     * @When /^I bulk delete products "([^"]*)"$/
     */
    public function bulkDeleteProducts($productReferences)
    {
        $storage = SharedStorage::getStorage();
        $ids = [];
        foreach (explode(',', $productReferences) as $productReference) {
            /** @var Product $productFromStorage */
            $productFromStorage = $storage->get($productReference);

            $ids[] = (int) $productFromStorage->id;
        }

        $this->getCommandBus()->handle(new BulkDeleteProductCommand($ids));
    }

    /**
     * @When /^duplicate product "([^"]*)"$/
     */
    public function duplicateProduct($productReferences)
    {
        /** @var Product $product */
        $product = SharedStorage::getStorage()->get($productReferences);
        $this->getCommandBus()->handle(new DuplicateProductCommand((int) $product->id));
    }

    /**
     * @When /^bulk duplicate product "([^"]*)"$/
     */
    public function bulkDuplicateProduct($productReferences)
    {
        $storage = SharedStorage::getStorage();
        $ids = [];
        foreach (explode(',', $productReferences) as $productReference) {
            /** @var Product $productFromStorage */
            $productFromStorage = $storage->get($productReference);

            $ids[] = (int) $productFromStorage->id;
        }

        $this->getCommandBus()->handle(new BulkDuplicateProductCommand($ids));
    }
}
