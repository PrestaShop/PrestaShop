<?php

namespace Tests\Integration\Behaviour\Features\Context;

use Behat\Behat\Context\Context as BehatContext;
use Context;
use LegacyTests\Unit\Core\Cart\Calculation\CartOld;

class CartFeatureContext implements BehatContext
{

    use CartAwareTrait;

    /**
     * @var CartOld
     */
    protected $cart;

    /**
     * @Given I have an empty default cart
     */
    public function iHaveAnEmptyDefaultCart()
    {
        $cart = new CartOld();
        $cart->id_lang = (int)Context::getContext()->language->id;
        $cart->id_currency = (int)Context::getContext()->currency->id;
        $cart->id_shop = (int)Context::getContext()->shop->id;
        $cart->add(); // required, else we cannot get the content when calculating total
        Context::getContext()->cart = $cart;
    }

    /**
     * This hook can be used to perform a database cleaning of added objects
     *
     * @AfterScenario
     */
    public function afterScenario_emptyCartProducts()
    {
        if ($this->getCurrentCart() !== null) {
            // remove products from cart
            $productDatas = $this->getCurrentCart()->getProducts(true);
            foreach ($productDatas as $productData) {
                $this->getCurrentCart()->updateQty(0, $productData['id_product'], $productData['id_product_attribute']);
            }
        }
    }

}
