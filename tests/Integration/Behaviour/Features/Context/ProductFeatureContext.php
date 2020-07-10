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

namespace Tests\Integration\Behaviour\Features\Context;

use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\TableNode;
use Cart;
use Combination;
use Configuration;
use Context;
use Customization;
use CustomizationField;
use Pack;
use Product;
use SpecificPrice;
use StockAvailable;
use TaxRulesGroup;

class ProductFeatureContext extends AbstractPrestaShopFeatureContext
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
     * @var SpecificPrice[][]
     */
    protected $specificPrices = [];

    /**
     * @var Customization[]
     */
    protected $customizationsInCart = [];

    /**
     * @var CustomizationField[][]
     */
    protected $customizationFields = [];

    /**
     * @var CategoryFeatureContext
     */
    protected $categoryFeatureContext;

    /** @BeforeScenario */
    public function before(BeforeScenarioScope $scope)
    {
        $this->categoryFeatureContext = $scope->getEnvironment()->getContext(CategoryFeatureContext::class);
    }

    /* PRODUCTS */

    /**
     * @param $productName
     *
     * @return Product
     */
    public function getProductWithName($productName)
    {
        return $this->products[$productName];
    }

    /**
     * @Given /^there is a product in the catalog named "(.+)" with a price of (\d+\.\d+) and (\d+) items in stock$/
     */
    public function thereIsAProductWithNameAndPriceAndQuantity($productName, $price, $productQuantity)
    {
        $this->createProduct($productName, $price, $productQuantity);
    }

    /**
     * @When /^I add (\d+) items? of product "(.+)" in my cart$/
     */
    public function iAddProductNamedInMyCartWithQuantity($productQuantity, $productName)
    {
        $this->checkProductWithNameExists($productName);
        $result = $this->getCurrentCart()->updateQty($productQuantity, $this->products[$productName]->id);
        if (!$result) {
            throw new \RuntimeException(sprintf('Expects true, got %s instead', $result));
        }
    }

    /**
     * @When /^I change quantity of product "(.+)" in my cart with quantity (\d+) and operator (up|down|nothing), result of change is (OK|KO)$/
     */
    public function iChangeProductQuantityInMyCart($productName, $productQuantity, $operator, $expectedStr)
    {
        $this->checkProductWithNameExists($productName);
        $expected = $expectedStr == 'OK';
        $result = $this->getCurrentCart()->updateQty($productQuantity, $this->products[$productName]->id, null, false, $operator);
        if ($expected != $result) {
            throw new \RuntimeException(sprintf('Expects %s, got %s instead', $expected, $result));
        }
    }

    /**
     * @Then /^my cart should contain (\d+) units of product "(.+)", (excluding|including) items in pack$/
     */
    public function quantityOfProductNamedInMyCartShouldBe($productQuantity, $productName, $packItemsIncluded = null)
    {
        if ($packItemsIncluded != 'including') {
            $nbProduct = $this->getCurrentCart()->getProductQuantity($this->products[$productName]->id, null, null);
            if ($productQuantity != $nbProduct['quantity']) {
                throw new \RuntimeException(sprintf('Expects %s, got %s instead (excluding items in pack)', $productQuantity, $nbProduct['quantity']));
            }
        } else {
            $nbProduct = $this->getCurrentCart()->getProductQuantity($this->products[$productName]->id, null, null);
            if ($productQuantity != $nbProduct['deep_quantity']) {
                throw new \RuntimeException(sprintf('Expects %s, got %s instead (including items in pack)', $productQuantity, $nbProduct['deep_quantity']));
            }
        }
    }

    /**
     * @Then /^the remaining available stock for product "(.+)" should be ([\-\d]+)$/
     */
    public function remainingQuantityOfProductNamedShouldBe($productName, $productQuantity)
    {
        $this->checkProductWithNameExists($productName);
        // Be careful this counts the amount present in the cart as well event if the stock has not been updated yet
        $nbProduct = Product::getQuantity($this->products[$productName]->id, null, null, $this->getCurrentCart(), null);
        if ($productQuantity != $nbProduct) {
            throw new \RuntimeException(sprintf('Expects %s, got %s instead', $productQuantity, $nbProduct));
        }
    }

    /**
     * @Then /^the available stock for product "(.+)" should be ([\-\d]+)$/
     */
    public function actualQuantityOfProductNamedShouldBe($productName, $productQuantity)
    {
        $this->checkProductWithNameExists($productName);
        $nbProduct = StockAvailable::getQuantityAvailableByProduct($this->products[$productName]->id, null);
        if ($productQuantity != $nbProduct) {
            throw new \RuntimeException(sprintf('Expects %s, got %s instead', $productQuantity, $nbProduct));
        }
    }

    /**
     * @Then /^the available stock for combination "(.+)" of product "(.+)" should be ([\-\d]+)$/
     */
    public function actualQuantityOfCombinationNamedShouldBe($combinationName, $productName, $combinationQuantity)
    {
        $this->checkProductWithNameExists($productName);
        $this->checkCombinationWithNameExists($productName, $combinationName);
        $nbProduct = StockAvailable::getQuantityAvailableByProduct($this->products[$productName]->id, $this->combinations[$productName][$combinationName]->id);
        if ($combinationQuantity != $nbProduct) {
            throw new \RuntimeException(sprintf('Expects %s, got %s instead', $combinationQuantity, $nbProduct));
        }
    }

    /**
     * @Then /^I am not allowed to add (\d+) items of product "(.+)" in my cart$/
     */
    public function iAmNotAbleToAddProductNamedInMyCartWithQuantity($productQuantity, $productName)
    {
        $result = $this->getCurrentCart()->updateQty($productQuantity, $this->products[$productName]->id);
        if ($result) {
            throw new \RuntimeException(sprintf('Expects false, got %s instead', $result));
        }
    }

    protected function createProduct($productName, $price, $productQuantity)
    {
        if (isset($this->products[$productName])) {
            throw new \Exception('Product named "' . $productName . '" was already added in fixtures');
        }
        $product = new Product();
        $product->price = $price;
        $product->name = $productName;
        $product->quantity = $productQuantity;
        // Use same default tax rules group as products from fixtures to have the same tax rate
        $product->id_tax_rules_group = TaxRulesGroup::getIdByName('US-FL Rate (6%)');
        $productAdded = $product->add();
        if (!$productAdded) {
            throw new \RuntimeException('Could not add product in database');
        }
        StockAvailable::setQuantity((int) $product->id, 0, $product->quantity);

        $this->products[$productName] = $product;

        // Fix issue pack cache is set when adding products.
        Pack::resetStaticCache();
    }

    /**
     * This hook can be used to perform a database cleaning of added objects
     *
     * @AfterScenario
     */
    public function cleanProductFixtures()
    {
        foreach ($this->products as $product) {
            $product->delete();
        }
        $this->products = [];
    }

    /**
     * @Given /^product "(.+)" is out of stock$/
     *
     * @param string $productName
     */
    public function productWithNameIsOutOfStock($productName)
    {
        $this->checkProductWithNameExists($productName);
        $this->products[$productName]->quantity = 0;
        $this->products[$productName]->out_of_stock = 0;
        $this->products[$productName]->save();
        StockAvailable::setQuantity($this->products[$productName]->id, 0, 0);
        StockAvailable::setProductOutOfStock((int) $this->products[$productName]->id, 0);
    }

    /**
     * @Given /^product "(.+)" can be ordered out of stock$/
     *
     * @param string $productName
     */
    public function productWithNameCanBeOrderedOutOfStock($productName)
    {
        $this->checkProductWithNameExists($productName);
        StockAvailable::setProductOutOfStock($this->products[$productName]->id, 1);
    }

    /**
     * @param $productName
     */
    public function checkProductWithNameExists($productName)
    {
        $this->checkFixtureExists($this->products, 'Product', $productName);
    }

    /* SPECIFIC PRICE */

    /**
     * @Given /^product "(.+)" has a specific price named "(.+)" with an amount discount of (\d+\.\d+)$/
     */
    public function productWithNameHasASpecificPriceWithAmountDiscount($productName, $specificPriceName, $specificPriceDiscount)
    {
        if (isset($this->specificPrices[$productName][$specificPriceName])) {
            throw new \Exception('Product named "' . $productName . '" has already a specific price named "' . $specificPriceName . '"');
        }
        $specificPrice = new SpecificPrice();
        $specificPrice->id_product = $this->products[$productName]->id;
        $specificPrice->price = -1;
        $specificPrice->reduction = $specificPriceDiscount;
        $specificPrice->reduction_type = 'amount';
        $specificPrice->from_quantity = 1;
        $specificPrice->from = '0000-00-00 00:00:00';
        $specificPrice->to = '0000-00-00 00:00:00';
        // set required values from default
        $specificPrice->id_shop = (int) Context::getContext()->shop->id;
        $specificPrice->id_currency = (int) Context::getContext()->currency->id;
        $specificPrice->id_country = (int) Context::getContext()->country->id;
        $specificPrice->id_group = (int) Context::getContext()->customer->id_shop_group;
        $specificPrice->id_customer = (int) Context::getContext()->customer->id;
        $specificPrice->add();
        $this->specificPrices[$productName][$specificPriceName] = $specificPrice;
    }

    /**
     * This hook can be used to perform a database cleaning of added objects
     *
     * @AfterScenario
     */
    public function cleanSpecificPriceFixtures()
    {
        foreach ($this->specificPrices as $productName => $specificPrices) {
            foreach ($specificPrices as $specificPriceName => $specificPrice) {
                $specificPrice->delete();
            }
        }
        $this->specificPrices = [];
    }

    /**
     * @param $productName
     * @param $specificPriceName
     */
    public function checkSpecificPriceWithNameExists($productName, $specificPriceName)
    {
        $this->checkFixtureExists($this->specificPrices[$productName], 'SpecificPrice', $specificPriceName);
    }

    /**
     * @When /^product "(.+)" has following tax rule group id: (\d+)$/
     */
    public function setProductTaxRuleGroupId($productName, $taxRuleGroupId)
    {
        $this->checkProductWithNameExists($productName);
        $this->products[$productName]->id_tax_rules_group = $taxRuleGroupId;
        $this->products[$productName]->save();
    }

    /* COMBINATION */

    /**
     * @Given /^product "(.+)" has a combination named "(.+)" with (.*) items in stock$/
     */
    public function productWithNameHasACombinationWithNameAndQuantity($productName, $combinationName, $combinationQuantity)
    {
        if (isset($this->combinations[$productName][$combinationName])) {
            throw new \Exception('Product named "' . $productName . '" has already a combination named "' . $combinationName . '"');
        }
        $combination = new Combination();
        $combination->reference = $combinationName;
        $combination->id_product = $this->products[$productName]->id;
        $combination->quantity = $combinationQuantity;
        $combination->add();
        StockAvailable::setQuantity((int) $this->products[$productName]->id, $combination->id, $combination->quantity);
        $this->combinations[$productName][$combinationName] = $combination;
    }

    /**
     * @Given /^product "(.+)" has combinations with following details:$/
     */
    public function productWithNameHasCombinationsWithFollowingDetails($productName, TableNode $table)
    {
        $this->checkProductWithNameExists($productName);
        $productId = (int) $this->products[$productName]->id;
        $combinationsList = $table->getColumnsHash();
        $attributesList = \Attribute::getAttributes((int) Configuration::get('PS_LANG_DEFAULT'));
        foreach ($combinationsList as $combinationDetails) {
            $combinationName = $combinationDetails['reference'];
            $combination = new Combination();
            $combination->reference = $combinationName;
            $combination->id_product = $productId;
            $combination->quantity = (int) $combinationDetails['quantity'];
            $combination->add();
            StockAvailable::setQuantity($productId, $combination->id, (int) $combination->quantity);
            $this->combinations[$productName][$combinationName] = $combination;
            $combinationAttributes = explode(';', $combinationDetails['attributes']);
            $combinationAttributesIds = [];
            foreach ($combinationAttributes as $combinationAttribute) {
                list($attributeGroup, $attributeName) = explode(':', $combinationAttribute);
                foreach ($attributesList as $attributeDetail) {
                    if ($attributeDetail['attribute_group'] == $attributeGroup && $attributeDetail['name'] == $attributeName) {
                        $combinationAttributesIds[] = (int) $attributeDetail['id_attribute'];
                        continue 2;
                    }
                }
            }
            $combination->setAttributes($combinationAttributesIds);
        }
    }

    /**
     * @Then /^the remaining available stock for combination "(.+)" of product "(.+)" should be ([\-\d]+)$/
     */
    public function remainingQuantityOfCombinationNamedForProductNamedShouldBe($combinationName, $productName, $combinationQuantity)
    {
        $this->checkProductWithNameExists($productName);
        $this->checkCombinationWithNameExists($productName, $combinationName);
        $nbProduct = Product::getQuantity($this->products[$productName]->id, $this->combinations[$productName][$combinationName]->id, null, $this->getCurrentCart(), null);
        if ($combinationQuantity != $nbProduct) {
            throw new \RuntimeException(sprintf('Expects %s, got %s instead', $combinationQuantity, $nbProduct));
        }
    }

    /**
     * @When /^I add (\d+) items of combination "(.+)" of product "(.+)"$/
     */
    public function iAddCombinationNamedOfProductNamedInMyCartWithQuantity($combinationQuantity, $combinationName, $productName)
    {
        $this->checkProductWithNameExists($productName);
        $this->checkCombinationWithNameExists($productName, $combinationName);
        $result = $this->getCurrentCart()->updateQty($combinationQuantity, $this->products[$productName]->id, $this->combinations[$productName][$combinationName]->id);
        if (!$result) {
            throw new \RuntimeException(sprintf('Expects true, got %s instead', $result));
        }
    }

    /**
     * @Then /^I am not able to add (\d+) items of combination "(.+)" of product "(.+)" in my cart$/
     */
    public function iAmNotAbleToAddPCombinationNamedOfroductNamedInMyCartWithQuantity($combinationQuantity, $combinationName, $productName)
    {
        $this->checkProductWithNameExists($productName);
        $this->checkCombinationWithNameExists($productName, $combinationName);
        $result = $this->getCurrentCart()->updateQty($combinationQuantity, $this->products[$productName]->id, $this->combinations[$productName][$combinationName]->id);
        if ($result) {
            throw new \RuntimeException(sprintf('Expects false, got %s instead', $result));
        }
    }

    /**
     * @Then /^I should have (\d+) items of combination "(.+)" of product "(.+)" in my cart$/
     */
    public function quantityOfCombinationNamedOfProductNamedInMyCartShouldBe($combinationQuantity, $combinationName, $productName)
    {
        $this->checkProductWithNameExists($productName);
        $this->checkCombinationWithNameExists($productName, $combinationName);
        $nbProduct = $this->getCurrentCart()->getProductQuantity($this->products[$productName]->id, $this->combinations[$productName][$combinationName]->id, null);
        if ($combinationQuantity != $nbProduct['quantity']) {
            throw new \RuntimeException(sprintf('Expects %s, got %s instead', $combinationQuantity, $nbProduct['quantity']));
        }
    }

    /**
     * This hook can be used to perform a database cleaning of added objects
     *
     * @AfterScenario
     */
    public function cleanCombinationFixtures()
    {
        foreach ($this->combinations as $productName => $combinations) {
            foreach ($combinations as $combinationName => $combination) {
                $combination->delete();
            }
        }
        $this->combinations = [];
    }

    /**
     * @param $productName
     * @param $combinationName
     */
    public function checkCombinationWithNameExists($productName, $combinationName)
    {
        $this->checkFixtureExists($this->combinations[$productName], 'Combination', $combinationName);
    }

    /* CUSTOMIZATION */

    /**
     * @Given /^product "(.+)" has a customization field named "(.+)"$/
     */
    public function productWithNameHasACustomizationWithName($productName, $customizationFieldName)
    {
        $this->checkProductWithNameExists($productName);
        $this->products[$productName]->customizable = 1;
        $this->products[$productName]->save();

        $customizationField = new CustomizationField();
        $customizationField->id_product = $this->products[$productName]->id;
        $customizationField->type = 1; // text field
        $customizationField->required = 1;
        $customizationField->name = [
            (int) Configuration::get('PS_LANG_DEFAULT') => $customizationFieldName,
        ];
        $customizationField->add();
        $this->customizationFields[$productName][$customizationFieldName] = $customizationField;
    }

    /**
     * @Then /^the remaining available stock for customization "(.*)" of product "(.*)" should be (.*)$/
     */
    public function remainingQuantityOfCustomizationNamedForProductNamedShouldBe($customizationFieldName, $productName, $customizationQuantity)
    {
        $this->checkProductWithNameExists($productName);
        $this->checkCustomizationWithNameExists($productName, $customizationFieldName);
        $nbProduct = Product::getQuantity($this->products[$productName]->id, null, null, $this->getCurrentCart(), $this->customizationsInCart[$productName]->id);
        if ($customizationQuantity != $nbProduct) {
            throw new \RuntimeException(sprintf('Expects %s, got %s instead', $customizationQuantity, $nbProduct));
        }
    }

    /**
     * @When /^I add (\d+) items of customization "(.+)" of product "(.+)"$/
     */
    public function iAddCustomizationNamedOfProductNamedInMyCartWithQuantity($customizationFieldQuantity, $customizationFieldName, $productName)
    {
        $this->checkProductWithNameExists($productName);
        $this->addCustomizationInCurrentCartForProductNamedIfNotExist($productName);
        $result = $this->getCurrentCart()->updateQty($customizationFieldQuantity, $this->products[$productName]->id, null, $this->customizationsInCart[$productName]->id);
        if (!$result) {
            throw new \RuntimeException(sprintf('Expects true, got %s instead', $result));
        }
    }

    /**
     * @Then /^I am not able to add (\d+) items of customization "(.+)" of product "(.+)" to my cart$/
     */
    public function iAmNotAbleToAddPCustomizationNamedOfroductNamedInMyCartWithQuantity($customizationFieldQuantity, $customizationFieldName, $productName)
    {
        $this->checkProductWithNameExists($productName);
        $this->checkCustomizationWithNameExists($productName, $customizationFieldName);
        $this->addCustomizationInCurrentCartForProductNamedIfNotExist($productName);
        $result = $this->getCurrentCart()->updateQty($customizationFieldQuantity, $this->products[$productName]->id, null, $this->customizationFields[$productName][$customizationFieldName]->id);
        if ($result) {
            throw new \RuntimeException(sprintf('Expects false, got %s instead', $result));
        }
    }

    /**
     * @Then /^I should have (\d+) items of customization "(.+)" of product "(.+)" in my cart$/
     */
    public function quantityOfCustomizationNamedOfProductNamedInMyCartShouldBe($customizationFieldQuantity, $customizationFieldName, $productName)
    {
        $this->checkProductWithNameExists($productName);
        $this->checkCustomizationWithNameExists($productName, $customizationFieldName);
        $nbProduct = $this->getCurrentCart()->getProductQuantity($this->products[$productName]->id, null, $this->customizationsInCart[$productName]->id);
        if ($customizationFieldQuantity != $nbProduct['quantity']) {
            throw new \RuntimeException(sprintf('Expects %s, got %s instead', $customizationFieldQuantity, $nbProduct['quantity']));
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
    public function cleanCustomizationFixtures()
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

    /**
     * @param $productName
     * @param $customizationFieldName
     */
    public function checkCustomizationWithNameExists($productName, $customizationFieldName)
    {
        $this->checkFixtureExists($this->customizationFields[$productName], 'Customization field', $customizationFieldName);
    }

    /**
     * @param $productName
     * @param $customizationFieldName
     */
    public function checkCustomizationIsInCart($productName)
    {
        $this->checkFixtureExists($this->customizationsInCart, 'Customization for product named ' . $productName, $productName);
    }

    /* PACK */

    /**
     * @Given /^product "(.+)" is a pack containing (\d+) items of product "(.+)"$/
     */
    public function productWithNameIsAPackContainingQuantityOfProductNamed($packName, $containedQuantity, $containedProductName)
    {
        $this->checkProductWithNameExists($packName);
        $this->checkProductWithNameExists($containedProductName);
        Pack::addItem(
            $this->products[$packName]->id,
            $this->products[$containedProductName]->id,
            $containedQuantity
        );
    }

    /**
     * @Then /^pack "(.+)" has (enough stock|not enough stock) for an order of (\d+) items$/
     */
    public function packWithNameIsInStockForQuantity($packName, $enoughStock, $packQuantity)
    {
        $this->checkProductWithNameExists($packName);
        $result = Pack::isInStock($this->products[$packName]->id, $packQuantity);
        switch ($enoughStock) {
            case 'enough stock':
                $expected = true;
                break;
            case 'not enough stock':
                $expected = false;
                break;
            default:
                throw new \Exception('Unknown stock status: ' . $enoughStock);
                break;
        }
        if ($result !== $expected) {
            throw new \RuntimeException(sprintf('Expects %s, got %s instead', $enoughStock, $result ? 'enough stock' : 'not enough stock'));
        }
    }

    /**
     * @Then /^product "(.+)" is considered as a pack$/
     */
    public function productIsConsideredAsAPack($productName)
    {
        $this->checkProductWithNameExists($productName);
        if (!Pack::isPack($this->products[$productName]->id)) {
            throw new \RuntimeException(sprintf('Expects %s to be considered as a pack, it is not', $productName));
        }
    }

    /**
     * @Given /^product "(.+)" is virtual$/
     */
    public function productWithNameProductIsVirtual($productName)
    {
        $this->checkProductWithNameExists($productName);
        $this->products[$productName]->is_virtual = 1;
        $this->products[$productName]->save();
    }

    /**
     * @Given /^product "(.+?)" is in category "(.+?)"$/
     */
    public function productWithNameProductInInCategory($productName, $categoryName)
    {
        $this->checkProductWithNameExists($productName);
        $this->categoryFeatureContext->checkCategoryWithNameExists($categoryName);

        $category = $this->categoryFeatureContext->getCategoryWithName($categoryName);

        $this->products[$productName]->id_category_default = $category->id_category;
        $this->products[$productName]->addToCategories([$category->id]);
        $this->products[$productName]->save();
    }

    /**
     * @Then The price of each product :productName after reduction should be :priceWithReduction
     */
    public function productPriceAfterReduction($productName, $priceWithReduction)
    {
        $this->checkProductWithNameExists($productName);
        $productPricesList = $this->getCurrentCart()->getProducts(true);

        foreach ($productPricesList as $productPrices) {
            if ($this->products[$productName]->id == $productPrices['id_product'] && $productPrices['price_with_reduction'] != $priceWithReduction) {
                throw new \RuntimeException(sprintf('Expects %s, got %s instead', $priceWithReduction, $productPrices['price_with_reduction']));
            }
        }
    }
}
