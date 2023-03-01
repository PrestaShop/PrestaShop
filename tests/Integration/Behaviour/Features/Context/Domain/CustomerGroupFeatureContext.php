<?php

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use Exception;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Command\AddCustomerGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Query\GetCustomerGroupForEditing;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\QueryResult\EditableCustomerGroup;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\ValueObject\GroupId;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductForEditing;
use Tests\Integration\Behaviour\Features\Context\CommonFeatureContext;
use Tests\Integration\Behaviour\Features\Context\Domain\Product\UpdateTagsFeatureContext;
use Tests\Integration\Behaviour\Features\Context\Util\DataComparator;

class CustomerGroupFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * "When" steps perform actions, and some of them store the latest result
     * in this variable so that "Then" action can check its content
     *
     * @var mixed
     */
    protected $latestResult;

    /**
     * Registry to keep track of created/edited customer groups using references
     *
     * @var int[]
     */
    protected $registry = [];

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
            $data['reduction'],
            $data['priceDisplayMethod'],
            $data['showPrice']
        );

        /** @var GroupId $id */
        $id = $commandBus->handle($command);
        $this->latestResult = $id->getValue();
        $this->registry[$customerGroupReference] = $id->getValue();
    }

    /**
     * @When /^I query Customer Group "(.+)" I should get a Customer Group with properties:$/
     */
    public function assertQueryCustomerProperties($customerGroupReference, TableNode $table)
    {
        $expectedData = $table->getRowsHash();

        $queryBus = $this->getQueryBus();
        /** @var EditableCustomerGroup $result */
        $result = $queryBus->handle(new GetCustomerGroupForEditing($this->registry[$customerGroupReference]));

        $serializer = CommonFeatureContext::getContainer()->get('serializer');
        $realData = $serializer->normalize($result);

        DataComparator::assertDataSetsAreIdentical($expectedData, $realData);

        $this->latestResult = null;
    }

    /**
     * @Then customer group :productReference localized :fieldName should be:
     *
     * localizedValues transformation handled by @see LocalizedArrayTransformContext
     *
     * @param string $customerGroupReference
     * @param string $fieldName
     * @param array $expectedLocalizedValues
     */
    public function assertLocalizedPropertyForCustomerGroup(string $customerGroupReference, string $fieldName, array $expectedLocalizedValues): void
    {
        $queryBus = $this->getQueryBus();
        /** @var EditableCustomerGroup $result */
        $result = $queryBus->handle(new GetCustomerGroupForEditing($this->registry[$customerGroupReference]));

        $this->assertLocalizedProperty($result, $fieldName, $expectedLocalizedValues);
    }

    private function assertLocalizedProperty(EditableCustomerGroup $editableCustomerGroup, string $fieldName, array $expectedLocalizedValues): void
    {
        foreach ($expectedLocalizedValues as $langId => $expectedValue) {
            $actualValues = $this->extractValueFromProductForEditing($editableCustomerGroup, $fieldName);
            $langIso = Language::getIsoById($langId);

            if (!isset($actualValues[$langId])) {
                throw new RuntimeException(sprintf(
                    'Expected localized %s value is not set in %s language',
                    $fieldName,
                    $langIso
                ));
            }

            $actualValue = $actualValues[$langId];

            if ($expectedValue !== $actualValue) {
                throw new RuntimeException(
                    sprintf(
                        'Expected %s in "%s" language was "%s", but got "%s"',
                        $fieldName,
                        $langIso,
                        var_export($expectedValue, true),
                        var_export($actualValue, true)
                    )
                );
            }
        }
    }

    /**
     * @return CommandBusInterface
     */
    protected function getCommandBus(): CommandBusInterface
    {
        return CommonFeatureContext::getContainer()->get('prestashop.core.command_bus');
    }
}
