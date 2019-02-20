<?php

namespace Tests\Integration\Behaviour\Features\Context;

use Behat\Behat\Context\Context as BehatContext;
use Behat\Behat\Tester\Exception\PendingException;
use Combination;
use Pack;
use Product;
use StockAvailable;

class ProductFeatureContext implements BehatContext
{

    use CartAwareTrait;

    /**
     * @var Product[]
     */
    protected $products = [];

    /**
     * @var Combination[][]
     */
    protected $combinations = [];

    /**
     * @Given there is a product with name :productName and price :price and quantity :quantity
     */
    public function thereIsAProductWithNameAndPriceAndQuantity($productName, $price, $quantity)
    {
        $this->createProduct($productName, $price, $quantity);
    }

    /**
     * @When I add product named :productName in my cart with quantity :quantity
     */
    public function iAddProductNamedInMyCartWithQuantity($productName, $quantity)
    {
        $result = $this->getCurrentCart()->updateQty($quantity, $this->products[$productName]->id);
        if (!$result) {
            throw new \RuntimeException(
                sprintf(
                    'Expects true, got %s instead',
                    $result
                )
            );
        }
    }

    /**
     * @When I change quantity of product named :productName in my cart with quantity :quantity and operator :operator, result of change is :expectedStr
     */
    public function iChangeProductQuantityInMyCarty($productName, $quantity, $operator, $expectedStr)
    {
        if (!isset($this->products[$productName])) {
            throw new \Exception('Product with name "' . $productName . '" was not added in fixtures');
        }
        $expected = $expectedStr == 'OK';
        $result = $this->getCurrentCart()->updateQty($quantity, $this->products[$productName]->id, null, false, $operator);
        if ($expected != $result) {
            throw new \RuntimeException(
                sprintf(
                    'Expects %s, got %s instead',
                    $expected,
                    $result
                )
            );
        }
    }

    /**
     * @Then Quantity of product named :productName in my cart should be :quantity
     */
    public function quantityOfProductNamedInMyCartShouldBe($productName, $quantity)
    {
        $nbProduct = $this->getCurrentCart()->getProductQuantity($this->products[$productName]->id, null, null);
        if ($quantity != $nbProduct['quantity']) {
            throw new \RuntimeException(
                sprintf(
                    'Expects %s, got %s instead',
                    $quantity,
                    $nbProduct['quantity']
                )
            );
        }
    }

    /**
     * @Then Remaining quantity of product named :productName should be :quantity
     */
    public function remainingQuantityOfProductNamedShouldBe($productName, $quantity)
    {
        if (!isset($this->products[$productName])) {
            throw new \Exception('Product with name "' . $productName . '" was not added in fixtures');
        }
        $nbProduct = Product::getQuantity($this->products[$productName]->id, null, null, $this->getCurrentCart(), null);
        if ($quantity != $nbProduct) {
            throw new \RuntimeException(
                sprintf(
                    'Expects %s, got %s instead',
                    $quantity,
                    $nbProduct
                )
            );
        }
    }

    /**
     * @Then I am not able to add product named :productName in my cart with quantity :quantity
     */
    public function iAmNotAbleToAddProductNamedInMyCartWithQuantity($productName, $quantity)
    {
        $result = $this->getCurrentCart()->updateQty($quantity, $this->products[$productName]->id);
        if ($result) {
            throw new \RuntimeException(
                sprintf(
                    'Expects false, got %s instead',
                    $result
                )
            );
        }
    }

    protected function createProduct($productName, $price, $quantity)
    {
        if (isset($this->products[$productName])) {
            throw new \Exception('Product with name "' . $productName . '" was already added in fixtures');
        }
        $product = new Product();
        $product->price = $price;
        $product->name = $productName;
        $product->quantity = $quantity;
        $product->add();
        StockAvailable::setQuantity((int)$product->id, 0, $product->quantity);

        $this->products[$productName] = $product;

        // Fix issue pack cache is set when adding products.
        Pack::resetStaticCache();
    }

    /**
     * This hook can be used to perform a database cleaning of added objects
     *
     * @AfterScenario
     */
    public function afterScenario_cleanProducts()
    {
        // delete products
        foreach ($this->products as $product) {
            $product->delete();
        }
        $this->products = [];
    }

    /**
     * This hook can be used to perform a database cleaning of added objects
     *
     * @AfterScenario
     */
    public function afterScenario_cleanCombinations()
    {
        // delete products
        foreach ($this->combinations as $productName => $combinations) {
            foreach ($combinations as $combinationName => $combination) {
                $combination->delete();
            }
        }
        $this->products = [];
    }

    /**
     * @Given product with name :productName is out of stock
     */
    public function productWithNameIsOutOfStock($productName)
    {
        $this->products[$productName]->out_of_stock = 1;
        $this->products[$productName]->save();
    }

    /**
     * @Given product with name :productName has a combination with name :combinationName and quantity :combinationQuantity
     */
    public function productWithNameHasACombinationWithNameAndQuantity($productName, $combinationName, $combinationQuantity)
    {
        if (isset($this->combinations[$productName][$combinationName])) {
            throw new \Exception('Product with name "' . $productName . '" has already a combination with name "' . $combinationName . '"');
        }
        $combination = new Combination();
        $combination->reference = $combinationName;
        $combination->id_product = $this->products[$productName]->id;
        $combination->quantity = $combinationQuantity;
        $combination->add();
        StockAvailable::setQuantity((int)$this->products[$productName]->id, $combination->id, $combination->quantity);
        $this->combinations[$productName][$combinationName] = $combination;
    }

    /**
     * @Then Remaining quantity of combination named :combinationName for product named :productName should be :combinationQuantity
     */
    public function remainingQuantityOfCombinationNamedForProductNamedShouldBe($combinationName, $productName, $combinationQuantity)
    {
        if (!isset($this->products[$productName])) {
            throw new \Exception('Product with name "' . $productName . '" was not added in fixtures');
        }
        if (!isset($this->combinations[$productName][$combinationName])) {
            throw new \Exception('Combination with name "' . $combinationName . '" for product with name "' . $productName . '" was not added in fixtures');
        }
        $nbProduct = Product::getQuantity($this->products[$productName]->id, $this->combinations[$productName][$combinationName]->id, null, $this->getCurrentCart(), null);
        if ($combinationQuantity != $nbProduct) {
            throw new \RuntimeException(
                sprintf(
                    'Expects %s, got %s instead',
                    $combinationQuantity,
                    $nbProduct
                )
            );
        }
    }

    /**
     * @When I add combination named :combinationName of product named :productName in my cart with quantity :combinationQuantity
     */
    public function iAddCombinationNamedOfProductNamedInMyCartWithQuantity($combinationName, $productName, $combinationQuantity)
    {
        if (!isset($this->products[$productName])) {
            throw new \Exception('Product with name "' . $productName . '" was not added in fixtures');
        }
        if (!isset($this->combinations[$productName][$combinationName])) {
            throw new \Exception('Combination with name "' . $combinationName . '" for product with name "' . $productName . '" was not added in fixtures');
        }
        $result = $this->getCurrentCart()->updateQty($combinationQuantity, $this->products[$productName]->id, $this->combinations[$productName][$combinationName]->id);
        if (!$result) {
            throw new \RuntimeException(
                sprintf(
                    'Expects true, got %s instead',
                    $result
                )
            );
        }
    }

    /**
     * @Then I am not able to add combination named :combinationName of product named :productName in my cart with quantity :quantity
     */
    public function iAmNotAbleToAddPCombinationNamedOfroductNamedInMyCartWithQuantity($combinationName, $productName, $quantity)
    {
        if (!isset($this->products[$productName])) {
            throw new \Exception('Product with name "' . $productName . '" was not added in fixtures');
        }
        if (!isset($this->combinations[$productName][$combinationName])) {
            throw new \Exception('Combination with name "' . $combinationName . '" for product with name "' . $productName . '" was not added in fixtures');
        }
        $result = $this->getCurrentCart()->updateQty($quantity, $this->products[$productName]->id, $this->combinations[$productName][$combinationName]->id);
        if ($result) {
            throw new \RuntimeException(
                sprintf(
                    'Expects false, got %s instead',
                    $result
                )
            );
        }
    }

    /**
     * @Then Quantity of combination named :combinationName of product named :productName in my cart should be :combinationQuantity
     */
    public function quantityOfCombinationNamedOfProductNamedInMyCartShouldBe($combinationName, $productName, $combinationQuantity)
    {
        if (!isset($this->products[$productName])) {
            throw new \Exception('Product with name "' . $productName . '" was not added in fixtures');
        }
        if (!isset($this->combinations[$productName][$combinationName])) {
            throw new \Exception('Combination with name "' . $combinationName . '" for product with name "' . $productName . '" was not added in fixtures');
        }
        $nbProduct = $this->getCurrentCart()->getProductQuantity($this->products[$productName]->id, $this->combinations[$productName][$combinationName]->id, null);
        if ($combinationQuantity != $nbProduct['quantity']) {
            throw new \RuntimeException(
                sprintf(
                    'Expects %s, got %s instead',
                    $combinationQuantity,
                    $nbProduct['quantity']
                )
            );
        }
    }
}
