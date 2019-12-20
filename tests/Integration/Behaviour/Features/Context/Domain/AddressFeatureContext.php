<?php

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\TableNode;

class AddressFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @Given I create new address with following details:
     *
     * @param TableNode $table
     */
    public function createNewAddressWithFollowingDetails(TableNode $table)
    {
        $data = $table->getRowsHash();
        throw new PendingException();
    }
}
