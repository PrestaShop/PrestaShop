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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use Configuration;
use Exception;
use Group;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\Customer\Command\AddCustomerCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Command\SetPrivateNoteAboutCustomerCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Command\SetRequiredFieldsForCustomerCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Exception\GroupNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Query\GetRequiredFieldsForCustomer;
use PrestaShop\PrestaShop\Core\Domain\Customer\Query\SearchCustomers;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;
use PrestaShop\PrestaShop\Core\Group\Provider\DefaultGroupsProviderInterface;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\CommonFeatureContext;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;

class CustomerFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * Random integer representing group id which should never exist in test database
     */
    private const NON_EXISTING_GROUP_ID = 74011211;

    /**
     * Random integer representing customer id which should never exist in test database
     */
    private const NON_EXISTING_CUSTOMER_ID = 8120552;

    /**
     * @Given /^"(Partner offers)" is "(required|not required)"$/
     * @Then /^"(Partner offers)" should be "(required|not required)"$/
     */
    public function validateRequiredFieldStatus($requiredField, $status)
    {
        $requiredFieldName = $this->getRequiredFieldName($requiredField);
        $isRequired = $status === 'required';

        $requiredFields = $this->getQueryBus()->handle(new GetRequiredFieldsForCustomer());

        if ($isRequired && !in_array($requiredFieldName, $requiredFields, true)) {
            throw new RuntimeException(sprintf('"%s" was expected to be required customer field.', $requiredField));
        }

        if (!$isRequired && in_array($requiredFieldName, $requiredFields, true)) {
            throw new RuntimeException(sprintf('"%s" was not expected to be required customer field.', $requiredField));
        }
    }

    /**
     * @Given /^I specify "(Partner offers)" to be "(required|not required)"$/
     */
    public function specifyRequiredField($requiredField, $status)
    {
        $requiredFieldName = $this->getRequiredFieldName($requiredField);
        $isRequired = $status === 'required';

        $requiredFields = $isRequired ? [$requiredFieldName] : [];

        $this->getSharedStorage()->set('customer_required_fields', $requiredFields);
    }

    /**
     * @When I save required fields for customer
     */
    public function saveSpecifiedRequiredFields()
    {
        $requiredFields = $this->getSharedStorage()->get('customer_required_fields');

        $this->getCommandBus()->handle(new SetRequiredFieldsForCustomerCommand($requiredFields));
    }

    /**
     * @When I set :privateNote private note about customer :reference
     */
    public function setPrivateNoteAboutCustomer($privateNote, $reference)
    {
        $customerId = $this->getSharedStorage()->get($reference);

        $this->getCommandBus()->handle(new SetPrivateNoteAboutCustomerCommand((int) $customerId, $privateNote));
    }

    /**
     * @param string $requiredField
     *
     * @return string
     */
    private function getRequiredFieldName($requiredField)
    {
        $requiredCustomerFields = [
            'Partner offers' => 'optin',
        ];

        return $requiredCustomerFields[$requiredField];
    }

    /**
     * @When /^I create customer "(.+)" with following details:$/
     *
     * @param string $customerReference
     * @param TableNode $table
     *
     * @throws Exception
     */
    public function createCustomerUsingCommand(string $customerReference, TableNode $table)
    {
        $data = $table->getRowsHash();

        $commandBus = $this->getCommandBus();

        /** @var DefaultGroupsProviderInterface $groupProvider */
        $groupProvider = CommonFeatureContext::getContainer()->get('prestashop.adapter.group.provider.default_groups_provider');
        $defaultGroups = $groupProvider->getGroups();

        $mandatoryFields = [
            'firstName',
            'lastName',
            'email',
            'password',
        ];

        foreach ($mandatoryFields as $mandatoryField) {
            if (!array_key_exists($mandatoryField, $data)) {
                throw new Exception(sprintf('Mandatory property %s for customer has not been provided', $mandatoryField));
            }
        }

        $command = new AddCustomerCommand(
            $data['firstName'],
            $data['lastName'],
            $data['email'],
            $data['password'],
            isset($data['defaultGroupId']) ? $data['defaultGroupId'] : $defaultGroups->getCustomersGroup()->getId(),
            isset($data['groupIds']) ? $data['groupIds'] : [$defaultGroups->getCustomersGroup()->getId()],
            (isset($data['shopId']) ? $data['shopId'] : 0),
            (isset($data['genderId']) ? $data['genderId'] : null),
            (isset($data['isEnabled']) ? $data['isEnabled'] : true),
            (isset($data['isPartnerOffersSubscribed']) ? $data['isPartnerOffersSubscribed'] : false),
            (isset($data['birthday']) ? $data['birthday'] : null)
        );

        if (Configuration::get('PS_B2B_ENABLE')) {
            $command->setCompanyName($data['companyName']);
        }

        /** @var CustomerId $id */
        $id = $commandBus->handle($command);
        SharedStorage::getStorage()->set($customerReference, $id->getValue());
    }

    /**
     * @Given customer :reference does not exist
     *
     * @param string $reference
     */
    public function setNonExistingCustomerReference(string $reference): void
    {
        if ($this->getSharedStorage()->exists($reference) && $this->getSharedStorage()->get($reference)) {
            throw new RuntimeException(sprintf('Expected that customer "%s" should not exist', $reference));
        }

        $this->getSharedStorage()->set($reference, self::NON_EXISTING_CUSTOMER_ID);
    }

    /**
     * @Then I should get error that customer was not found
     */
    public function assertCustomerNotFound(): void
    {
        $this->assertLastErrorIs(CustomerNotFoundException::class);
    }

    /**
     * @Given group :groupReference named :name exists
     *
     * @param string $groupReference
     * @param string $name
     */
    public function assertGroupExists(string $groupReference, string $name): void
    {
        $group = Group::searchByName($name);
        if (!$group) {
            throw new RuntimeException(sprintf('Group "%s" does not exist', $groupReference));
        }

        $this->getSharedStorage()->set($groupReference, (int) $group['id_group']);
    }

    /**
     * @Given group :reference does not exist
     *
     * @param string $reference
     */
    public function setNonExistingGroupReference(string $reference): void
    {
        if ($this->getSharedStorage()->exists($reference) && $this->getSharedStorage()->get($reference)) {
            throw new RuntimeException(sprintf('Expected that group "%s" should not exist', $reference));
        }

        $this->getSharedStorage()->set($reference, self::NON_EXISTING_GROUP_ID);
    }

    /**
     * @Then I should get error that group was not found
     */
    public function assertGroupNotFound(): void
    {
        $this->assertLastErrorIs(GroupNotFoundException::class);
    }

    /**
     * @Transform table:firstName,lastName,email,birthday
     * @Transform table:firstName,lastName,email,birthday,companyName
     *
     * @param TableNode $customersTable
     *
     * @return array
     */
    public function transformCustomers(TableNode $customersTable): array
    {
        return $customersTable->getHash();
    }

    /**
     * @When I search for the phrases :searchPhrases I should get the following results:
     *
     * @param string $searchPhrases
     * @param array $expectedCustomers
     */
    public function assertFoundCustomers(string $searchPhrases, array $expectedCustomers): void
    {
        $foundCustomers = $this->getQueryBus()->handle(new SearchCustomers(explode(' ', $searchPhrases)));
        $isB2BEnabled = Configuration::get('PS_B2B_ENABLE');

        foreach ($expectedCustomers as $currentExpectedCustomer) {
            $wasCurrentExpectedCustomerFound = false;
            foreach ($foundCustomers as $currentFoundCustomer) {
                if ($currentExpectedCustomer['email'] === $currentFoundCustomer['email']) {
                    $wasCurrentExpectedCustomerFound = true;

                    Assert::assertEquals(
                        $currentExpectedCustomer['firstName'],
                        $currentFoundCustomer['firstname'],
                        sprintf(
                            'Expected and found customers\'s first names don\'t match (%s and %s)',
                            $currentExpectedCustomer['firstName'],
                            $currentFoundCustomer['firstname']
                        )
                    );

                    Assert::assertEquals(
                        $currentExpectedCustomer['lastName'],
                        $currentFoundCustomer['lastname'],
                        sprintf(
                            'Expected and found customers\'s last names don\'t match (%s and %s)',
                            $currentExpectedCustomer['lastName'],
                            $currentFoundCustomer['lastname']
                        )
                    );

                    Assert::assertEquals(
                        $currentExpectedCustomer['birthday'],
                        $currentFoundCustomer['birthday'],
                        sprintf(
                            'Expected and found customers\'s birthdays don\'t match (%s and %s)',
                            $currentExpectedCustomer['birthday'],
                            $currentFoundCustomer['birthday']
                        )
                    );

                    if (!$isB2BEnabled) {
                        if (isset($currentExpectedCustomer['companyName'])
                            || isset($currentFoundCustomer['company'])
                        ) {
                            throw new RuntimeException(
                                'Company name isn\'t expected when B2B mode is disabled'
                            );
                        }
                    } else {
                        Assert::assertEquals(
                            $currentExpectedCustomer['companyName'],
                            $currentFoundCustomer['company'],
                            sprintf(
                                'Expected and found customers\'s companies don\'t match (%s and %s)',
                                $currentExpectedCustomer['companyName'],
                                $currentFoundCustomer['company']
                            )
                        );
                    }
                }
            }
            if (!$wasCurrentExpectedCustomerFound) {
                throw new RuntimeException(sprintf(
                    'Expected customer with email %s was not found',
                    $currentExpectedCustomer['email']
                ));
            }
        }
    }

    /**
     * @When I search for the phrases :searchPhrases I should not get any results
     *
     * @param string $searchPhrases
     */
    public function assertNoCustomersWasFound(string $searchPhrases): void
    {
        $foundCustomers = $this->getQueryBus()->handle(new SearchCustomers(explode(' ', $searchPhrases)));
        Assert::assertEmpty($foundCustomers);
    }
}
