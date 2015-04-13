<?php

class Adapter_ProductPriceCalculator
{
    public function getProductPrice(
        $id_product,
        $usetax = true,
        $id_product_attribute = null,
        $decimals = 6,
        $divisor = null,
		$only_reduc = false,
        $usereduc = true,
        $quantity = 1,
        $force_associated_tax = false,
        $id_customer = null,
        $id_cart = null,
		$id_address = null,
        &$specific_price_output = null,
        $with_ecotax = true,
        $use_group_reduction = true,
        Context $context = null,
		$use_customer_price = true
    )
    {
        return call_user_func_array(['Product', 'getPriceStatic'], func_get_args());
    }
}
