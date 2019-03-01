<?php

namespace Tests\Integration\Behaviour\Features\Context;

use Context;
use LegacyTests\Unit\Core\Cart\Calculation\CartOld;

/**
 * aims to provide access to cart through contexts
 */
trait CartAwareTrait
{

    /**
     * @return CartOld
     */
    public function getCurrentCart()
    {
        return Context::getContext()->cart;
    }
}
