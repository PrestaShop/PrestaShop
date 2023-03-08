<?php

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use Exception;
use PHPUnit\Framework\Assert;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Command\AddCustomerGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Query\GetCustomerGroupForEditing;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\QueryResult\EditableCustomerGroup;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\ValueObject\GroupId;
use Tests\Integration\Behaviour\Features\Context\CommonFeatureContext;

class CustomerGroupFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @param string $customerGroupReference
     *
     * @return EditableCustomerGroup
     */
    private function getCustomerGroupForEditing(string $customerGroupReference)
    {
        return $this->getQueryBus()->handle(new GetCustomerGroupForEditing($this->getSharedStorage()->get($customerGroupReference)));
    }

    /**
     * @return CommandBusInterface
     */
    protected function getCommandBus(): CommandBusInterface
    {
        return CommonFeatureContext::getContainer()->get('prestashop.core.command_bus');
    }

    /**
     * @When /^I create a Customer Group "(.+)" with the following details:$/
     *
     * @param string $customerGroupReference
     * @param TableNode $table
     *
     * @throws Exception
     */
    public function createCustomerUsingCommand(string $customerGroupReference, TableNode $table)
    {
        $data = $this->localizeByRows($table);
        $commandBus = $this->getCommandBus();

        $command = new AddCustomerGroupCommand(
            $data['name'],
            new DecimalNumber($data['reduction']),
            $data['displayPriceTaxExcluded'],
            $data['showPrice']
        );

        /** @var GroupId $id */
        $id = $commandBus->handle($command);
        $this->getSharedStorage()->set($customerGroupReference, $id->getValue());
    }

    /**
     * @When /^I query Customer Group "(.+)" I should get a Customer Group with properties:$/
     */
    public function assertQueryCustomerProperties($customerGroupReference, EditableCustomerGroup $expectedGroup)
    {
        Assert::assertEquals($this->getCustomerGroupForEditing($customerGroupReference), $expectedGroup);
    }

    /**
     * @Transform table:customer group,value
     *
     * @param TableNode $tableNode
     *
     * @return EditableCustomerGroup
     */
    public function transformEditableCustomerGroup(TableNode $tableNode): EditableCustomerGroup
    {
        $dataRows = $tableNode->getRowsHash();

        return new EditableCustomerGroup(
            $dataRows['id'],
            [
                1 => $dataRows['name[en-US]'],
                2 => $dataRows['name[fr-FR]'],
            ],
            new DecimalNumber($dataRows['reduction']),
            (bool) $dataRows['displayPriceTaxExcluded'],
            (bool) $dataRows['showPrice']
        );
    }
}
