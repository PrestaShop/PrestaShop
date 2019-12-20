<?php

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\TableNode;
use PrestaShop\PrestaShop\Core\Domain\Address\Command\SetRequiredFieldsForAddressCommand;

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

//        $this->getCommandBus()->handle(new SetRequiredFieldsForAddressCommand());
        throw new PendingException();
    }
}
