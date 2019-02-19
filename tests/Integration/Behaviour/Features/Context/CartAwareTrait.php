<?php

namespace Tests\Integration\Behaviour\Features\Context;

use Context;

/**
 * aims to provide access to cart through contexts
 */
trait CartAwareTrait
{
    public function getCurrentCart()
    {
        return Context::getContext()->cart;
    }
}
