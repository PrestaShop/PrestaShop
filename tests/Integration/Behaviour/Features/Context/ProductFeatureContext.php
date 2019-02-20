<?php

namespace Tests\Integration\Behaviour\Features\Context;

use Behat\Behat\Context\Context as BehatContext;
use Behat\Behat\Tester\Exception\PendingException;
use Cart;
use Combination;
use Configuration;
use Customization;
use CustomizationField;
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
     * @var Customization[]
     */
    protected $customizationsInCart = [];

    /**
     * @var CustomizationField[][]
     */
    protected $customizationFields = [];

    /* PRODUCTS */

    /**
     * @param $productName
     * @return bool
     */
    public function productWithNameExists($productName)
    {
        return isset($this->products[$productName]);
    }

    /**
     * @param $productName
     * @return Product
     */
    public function getProductWithName($productName)
    {
        return $this->products[$productName];
    }

    /**
     * @Given there is a product with name :productName and price :price and quantity :productQuantity
     */
    public function thereIsAProductWithNameAndPriceAndQuantity($productName, $price, $productQuantity)
    {
        $this->createProduct($productName, $price, $productQuantity);
    }

    /**
     * @When I add product named :productName in my cart with quantity :productQuantity
     */
    public function iAddProductNamedInMyCartWithQuantity($productName, $productQuantity)
    {
        $result = $this->getCurrentCart()->updateQty($productQuantity, $this->products[$productName]->id);
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
     * @When /^I change quantity of product named "(.*)" in my cart with quantity (\d+) and operator "(up|down|nothing)", result of change is (OK|KO)$/
     */
    public function iChangeProductQuantityInMyCart($productName, $productQuantity, $operator, $expectedStr)
    {
        if (!isset($this->products[$productName])) {
            throw new \Exception('Product with name "' . $productName . '" was not added in fixtures');
        }
        $expected = $expectedStr == 'OK';
        $result = $this->getCurrentCart()->updateQty($productQuantity, $this->products[$productName]->id, null, false, $operator);
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
     * @Then Quantity of product named :productName in my cart should be :productQuantity
     */
    public function quantityOfProductNamedInMyCartShouldBe($productName, $productQuantity)
    {
        $nbProduct = $this->getCurrentCart()->getProductQuantity($this->products[$productName]->id, null, null);
        if ($productQuantity != $nbProduct['quantity']) {
            throw new \RuntimeException(
                sprintf(
                    'Expects %s, got %s instead',
                    $productQuantity,
                    $nbProduct['quantity']
                )
            );
        }
    }

    /**
     * @Then Remaining quantity of product named :productName should be :productQuantity
     */
    public function remainingQuantityOfProductNamedShouldBe($productName, $productQuantity)
    {
        if (!isset($this->products[$productName])) {
            throw new \Exception('Product with name "' . $productName . '" was not added in fixtures');
        }
        $nbProduct = Product::getQuantity($this->products[$productName]->id, null, null, $this->getCurrentCart(), null);
        if ($productQuantity != $nbProduct) {
            throw new \RuntimeException(
                sprintf(
                    'Expects %s, got %s instead',
                    $productQuantity,
                    $nbProduct
                )
            );
        }
    }

    /**
     * @Then I am not able to add product named :productName in my cart with quantity :productQuantity
     */
    public function iAmNotAbleToAddProductNamedInMyCartWithQuantity($productName, $productQuantity)
    {
        $result = $this->getCurrentCart()->updateQty($productQuantity, $this->products[$productName]->id);
        if ($result) {
            throw new \RuntimeException(
                sprintf(
                    'Expects false, got %s instead',
                    $result
                )
            );
        }
    }

    protected function createProduct($productName, $price, $productQuantity)
    {
        if (isset($this->products[$productName])) {
            throw new \Exception('Product with name "' . $productName . '" was already added in fixtures');
        }
        $product = new Product();
        $product->price = $price;
        $product->name = $productName;
        $product->quantity = $productQuantity;
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
        foreach ($this->products as $product) {
            $product->delete();
        }
        $this->products = [];
    }

    /**
     * @Given product with name :productName is out of stock
     */
    public function productWithNameIsOutOfStock($productName)
    {
        if (!isset($this->products[$productName])) {
            throw new \Exception('Product with name "' . $productName . '" was not added in fixtures');
        }
        $this->products[$productName]->quantity     = 0;
        $this->products[$productName]->out_of_stock = 0;
        $this->products[$productName]->save();
        StockAvailable::setQuantity($this->products[$productName]->id, 0, 0);
        StockAvailable::setProductOutOfStock((int) $this->products[$productName]->id, 0);
    }

    /* COMBINATION */

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
     * @Then I am not able to add combination named :combinationName of product named :productName in my cart with quantity :combinationQuantity
     */
    public function iAmNotAbleToAddPCombinationNamedOfroductNamedInMyCartWithQuantity($combinationName, $productName, $combinationQuantity)
    {
        if (!isset($this->products[$productName])) {
            throw new \Exception('Product with name "' . $productName . '" was not added in fixtures');
        }
        if (!isset($this->combinations[$productName][$combinationName])) {
            throw new \Exception('Combination with name "' . $combinationName . '" for product with name "' . $productName . '" was not added in fixtures');
        }
        $result = $this->getCurrentCart()->updateQty($combinationQuantity, $this->products[$productName]->id, $this->combinations[$productName][$combinationName]->id);
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

    /**
     * This hook can be used to perform a database cleaning of added objects
     *
     * @AfterScenario
     */
    public function afterScenario_cleanCombinations()
    {
        foreach ($this->combinations as $productName => $combinations) {
            foreach ($combinations as $combinationName => $combination) {
                $combination->delete();
            }
        }
        $this->combinations = [];
    }

    /* CUSTOMIZATION */

    /**
     * @Given product with name :productName has a customization field with name :customizationFieldName
     */
    public function productWithNameHasACustomizationWithName($productName, $customizationFieldName)
    {
        if (!isset($this->products[$productName])) {
            throw new \Exception('Product with name "' . $productName . '" was not added in fixtures');
        }
        $this->products[$productName]->customizable = 1;
        $this->products[$productName]->save();

        $customizationField = new CustomizationField();
        $customizationField->id_product = $this->products[$productName]->id;
        $customizationField->type = 1; // text field
        $customizationField->required = 1;
        $customizationField->name = [
            (int)Configuration::get('PS_LANG_DEFAULT') => $customizationFieldName,
        ];
        $customizationField->add();
        $this->customizationFields[$productName][$customizationFieldName] = $customizationField;
    }

    /**
     * @Then Remaining quantity of customization named :customizationFieldName for product named :productName should be :customizationQuantity
     */
    public function remainingQuantityOfCustomizationNamedForProductNamedShouldBe($customizationFieldName, $productName, $customizationQuantity)
    {
        if (!isset($this->products[$productName])) {
            throw new \Exception('Product with name "' . $productName . '" was not added in fixtures');
        }
        if (!isset($this->customizationFields[$productName][$customizationFieldName])) {
            throw new \Exception('Customization field with name "' . $customizationFieldName . '" for product with name "' . $productName . '" was not added in fixtures');
        }
        $nbProduct = Product::getQuantity($this->products[$productName]->id, null, null, $this->getCurrentCart(), $this->customizationsInCart[$productName]->id);
        if ($customizationQuantity != $nbProduct) {
            throw new \RuntimeException(
                sprintf(
                    'Expects %s, got %s instead',
                    $customizationQuantity,
                    $nbProduct
                )
            );
        }
    }

    /**
     * @When I add customization named :customizationFieldName of product named :productName in my cart with quantity :customizationFieldQuantity
     */
    public function iAddCustomizationNamedOfProductNamedInMyCartWithQuantity($customizationFieldName, $productName, $customizationFieldQuantity)
    {
        if (!isset($this->products[$productName])) {
            throw new \Exception('Product with name "' . $productName . '" was not added in fixtures');
        }
        $this->addCustomizationInCurrentCartForProductNamedIfNotExist($productName);
        $result = $this->getCurrentCart()->updateQty($customizationFieldQuantity, $this->products[$productName]->id, null, $this->customizationsInCart[$productName]->id);
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
     * @Then I am not able to add customization named :customizationFieldName of product named :productName in my cart with quantity :customizationFieldQuantity
     */
    public function iAmNotAbleToAddPCustomizationNamedOfroductNamedInMyCartWithQuantity($customizationFieldName, $productName, $customizationFieldQuantity)
    {
        if (!isset($this->products[$productName])) {
            throw new \Exception('Product with name "' . $productName . '" was not added in fixtures');
        }
        if (!isset($this->customizationFields[$productName][$customizationFieldName])) {
            throw new \Exception('Customization field with name "' . $customizationFieldName . '" for product with name "' . $productName . '" was not added in fixtures');
        }
        $this->addCustomizationInCurrentCartForProductNamedIfNotExist($productName);
        $result = $this->getCurrentCart()->updateQty($customizationFieldQuantity, $this->products[$productName]->id, null, $this->customizationFields[$productName][$customizationFieldName]->id);
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
     * @Then Quantity of customization named :customizationFieldName of product named :productName in my cart should be :customizationFieldQuantity
     */
    public function quantityOfCustomizationNamedOfProductNamedInMyCartShouldBe($customizationFieldName, $productName, $customizationFieldQuantity)
    {
        if (!isset($this->products[$productName])) {
            throw new \Exception('Product with name "' . $productName . '" was not added in fixtures');
        }
        if (!isset($this->customizationsInCart[$productName])) {
            throw new \Exception('Customization for product with name "' . $productName . '" was not added in fixtures');
        }
        $nbProduct = $this->getCurrentCart()->getProductQuantity($this->products[$productName]->id, null, $this->customizationsInCart[$productName]->id);
        if ($customizationFieldQuantity != $nbProduct['quantity']) {
            throw new \RuntimeException(
                sprintf(
                    'Expects %s, got %s instead',
                    $customizationFieldQuantity,
                    $nbProduct['quantity']
                )
            );
        }
    }

    protected function addCustomizationInCurrentCartForProductNamedIfNotExist($productName)
    {
        if (isset($this->customizationsInCart[$productName])) {
            return;
        }

        $customization = new Customization();
        $customization->id_product = $this->products[$productName]->id;
        $customization->id_product_attribute = 0;
        $customization->id_address_delivery = 0;
        $customization->quantity = 0;
        $customization->quantity_refunded = 0;
        $customization->quantity_returned = 0;
        $customization->in_cart = 0;
        $customization->id_cart = $this->getCurrentCart()->id;
        $customization->add();

        $this->customizationsInCart[$productName] = $customization;
    }

    /**
     * This hook can be used to perform a database cleaning of added objects
     *
     * @AfterScenario
     */
    public function afterScenario_cleanCustomizations()
    {
        foreach ($this->customizationFields as $productName => $customizationFields) {
            foreach ($customizationFields as $customizationFieldName => $customizationField) {
                $customizationField->delete();
            }
        }
        $this->customizationFields = [];

        foreach ($this->customizationsInCart as $productName => $customization) {
            $customization->delete();
        }
        $this->customizationsInCart = [];
    }

    /* PACK */

    /**
     * @Given product with name :packName is a pack containing quantity :containedQuantity of product named :containedProductName
     */
    public function productWithNameIsAPackContainingQuantityOfProductNamed($packName, $containedQuantity, $containedProductName)
    {
        if (!isset($this->products[$packName])) {
            throw new \Exception('Product with name "' . $packName . '" was not added in fixtures');
        }
        if (!isset($this->products[$containedProductName])) {
            throw new \Exception('Product with name "' . $containedProductName . '" was not added in fixtures');
        }
        Pack::addItem(
            $this->products[$packName]->id,
            $this->products[$containedProductName]->id,
            $containedQuantity
        );
    }

    /**
     * @Then /^pack with name "([^"]*)" (is in stock|is not in stock) for quantity (\d+)$/
     */
    public function packWithNameIsInStockForQuantity($packName, $inStock, $packQuantity)
    {
        if (!isset($this->products[$packName])) {
            throw new \Exception('Product with name "' . $packName . '" was not added in fixtures');
        }
        $result = Pack::isInStock($this->products[$packName]->id, $packQuantity);
        switch ($inStock) {
            case 'is in stock':
                $expected = true;
                break;
            case 'is not in stock':
                $expected = false;
                break;
            default:
                throw new \Exception('Unknown stock status: ' . $inStock);
                break;
        }
        if ($result !== $expected) {
            throw new \RuntimeException(
                sprintf(
                    'Expects %s, got %s instead',
                    $inStock,
                    $result ? 'is in stock' : 'is not in stock'
                )
            );
        }
    }

    /**
     * @Then product :productName is considered as a pack
     */
    public function productIsConsideredAsAPack($productName)
    {
        if (!isset($this->products[$productName])) {
            throw new \Exception('Product with name "' . $productName . '" was not added in fixtures');
        }
        if (!Pack::isPack($this->products[$productName]->id)) {
            throw new \RuntimeException(
                sprintf(
                    'Expects %s to be considered as a pack, it is not',
                    $productName
                )
            );
        }
    }

    /**
     * @Then Deep quantity of product named :productName in my cart should be :productQuantity
     */
    public function deepQuantityOfProductNamedInMyCartShouldBe($productName, $productQuantity)
    {
        $nbProduct = $this->getCurrentCart()->getProductQuantity($this->products[$productName]->id, null, null);
        if ($productQuantity != $nbProduct['deep_quantity']) {
            throw new \RuntimeException(
                sprintf(
                    'Expects %s, got %s instead',
                    $productQuantity,
                    $nbProduct['deep_quantity']
                )
            );
        }
    }

}
