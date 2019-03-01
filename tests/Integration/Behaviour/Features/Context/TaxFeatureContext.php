<?php

namespace Tests\Integration\Behaviour\Features\Context;

use Behat\Behat\Context\Context as BehatContext;
use Context;
use Currency;
use Configuration;

class TaxFeatureContext implements BehatContext
{

    use CartAwareTrait;

    /**
     * @When /^I set delivery address id to (\d+)$/
     */
    public function setIdAddress($addressId)
    {
        $this->getCurrentCart()->id_address_delivery = $addressId;
    }

}
