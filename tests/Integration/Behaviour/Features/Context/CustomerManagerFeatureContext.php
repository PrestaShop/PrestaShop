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

namespace Tests\Integration\Behaviour\Features\Context;

use Behat\Gherkin\Node\TableNode;
use Customer;
use Exception;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Customer\Command\AddCustomerCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Command\DeleteCustomerCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Command\EditCustomerCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Command\TransformGuestToCustomerCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\DuplicateCustomerEmailException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Query\GetCustomerForEditing;
use PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult\EditableCustomer;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerDeleteMethod;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShop\PrestaShop\Core\Group\Provider\DefaultGroupsProviderInterface;
use Tests\Integration\Behaviour\Features\Context\Util\DataComparator;
use Tests\Integration\Behaviour\Features\Context\Util\DataTransfer;
use Tests\Integration\Behaviour\Features\Context\Util\NoExceptionAlthoughExpectedException;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

/**
 * CustomerManagerFeatureContext provides behat steps to perform actions related to prestashop customer management
 * and validate returned outputs
 */
class CustomerManagerFeatureContext extends AbstractPrestaShopFeatureContext
{
    /**
     * "When" steps perform actions, and some of them store the latest result
     * in this variable so that "Then" action can check its content
     *
     * @var mixed
     */
    protected $latestResult;

    /**
     * Registry to keep track of created/edited customers using references
     *
     * @var int[]
     */
    protected $customerRegistry = [];

    /**
     * @When /^I create a customer "(.+)" with following properties:$/
     */
    public function createACustomerUsingCommand($customerReference, TableNode $table)
    {
        $data = $table->getRowsHash();
        $data = $this->formatCustomerDataIfNeeded($data);

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

        /** @var CustomerId $id */
        $id = $commandBus->handle($command);

        $this->latestResult = $id->getValue();
        $this->customerRegistry[$customerReference] = $id->getValue();
    }

    /**
     * todo hint: move to domain context?
     *
     * @When /^I attempt to create a customer "(.+)" with following properties:$/
     */
    public function attemptToCreateACustomerUsingCommand($customerReference, TableNode $table)
    {
        try {
            $this->createACustomerUsingCommand($customerReference, $table);
            throw new NoExceptionAlthoughExpectedException();
        } catch (\Exception $e) {
            if ($e instanceof NoExceptionAlthoughExpectedException) {
                throw $e;
            }

            $this->latestResult = $e;
        }
    }

    /**
     * todo hint: move to domain context?
     *
     * @When I create not existing customer :customerReference with following properties:
     *
     * @param string $customerReference
     * @param TableNode $table
     */
    public function iCreateNotExistingCustomerWithFollowingProperties(string $customerReference, TableNode $table)
    {
        try {
            /** @var CustomerId $customerIdObject */
            $customerIdObject = $this->createACustomerUsingCommand($customerReference, $table);
            SharedStorage::getStorage()->set($customerReference, $customerIdObject->getValue());
        } catch (DuplicateCustomerEmailException $e) {
        }
    }

    /**
     * @When /^I edit customer "(.+)" and I change the following properties:$/
     */
    public function editCustomerUsingCommand($customerReference, TableNode $table)
    {
        $this->assertCustomerReferenceExistsInRegistry($customerReference);

        $data = $table->getRowsHash();
        $data = $this->formatCustomerDataIfNeeded($data);

        $commandBus = $this->getCommandBus();

        $command = new EditCustomerCommand($this->customerRegistry[$customerReference]);

        DataTransfer::transferAttributesFromArrayToObject($data, $command);
        $commandBus->handle($command);

        // Clear static cache or same cached groups will always be returned
        Customer::resetStaticCache();
    }

    /**
     * @When /^I transform guest "(.+)" into a customer$/
     */
    public function transformGuestIntoACustomer($customerReference)
    {
        $this->assertCustomerReferenceExistsInRegistry($customerReference);

        $commandBus = $this->getCommandBus();

        $command = new TransformGuestToCustomerCommand($this->customerRegistry[$customerReference]);
        $commandBus->handle($command);
    }

    /**
     * @When I delete customer ":customerReference" and allow it to register again
     */
    public function deleteCustomerWithAllowCustomerRegistration(string $customerReference): void
    {
        $this->deleteCustomer($customerReference, CustomerDeleteMethod::ALLOW_CUSTOMER_REGISTRATION);
    }

    /**
     * @When I delete customer ":customerReference" and prevent it from registering again
     */
    public function deleteCustomerWithDenyCustomerRegistration(string $customerReference): void
    {
        $this->deleteCustomer($customerReference, CustomerDeleteMethod::DENY_CUSTOMER_REGISTRATION);
    }

    /**
     * @param string $customerReference
     * @param string $methodName
     *
     * @throws \Exception
     */
    private function deleteCustomer(string $customerReference, string $methodName): void
    {
        $this->assertCustomerReferenceExistsInRegistry($customerReference);
        $this->validateDeleteCustomerMethod($methodName);

        $commandBus = $this->getCommandBus();

        $command = new DeleteCustomerCommand(
            $this->customerRegistry[$customerReference],
            $methodName
        );
        $commandBus->handle($command);
    }

    /**
     * @When /^I query customer "(.+)" I should get a Customer with properties:$/
     */
    public function assertQueryCustomerProperties($customerReference, TableNode $table)
    {
        $expectedData = $table->getRowsHash();
        $expectedData = $this->formatCustomerDataIfNeeded($expectedData);

        $this->assertCustomerReferenceExistsInRegistry($customerReference);

        $queryBus = $this->getQueryBus();
        /** @var EditableCustomer $result */
        $result = $queryBus->handle(new GetCustomerForEditing($this->customerRegistry[$customerReference]));

        $serializer = CommonFeatureContext::getContainer()->get('serializer');
        $realData = $serializer->normalize($result);

        DataComparator::assertDataSetsAreIdentical($expectedData, $realData);

        $this->latestResult = null;
    }

    /**
     * @When customer ":customerReference" should be soft deleted
     *
     * @param string $customerReference
     */
    public function checkSoftDeleted(string $customerReference): void
    {
        $customer = new Customer($this->customerRegistry[$customerReference]);
        Assert::assertTrue((bool) $customer->deleted);
    }

    /**
     * @Then the customer ":customerReference" should not be found
     */
    public function assertCustomerWasNotFound($customerReference)
    {
        $this->assertCustomerReferenceExistsInRegistry($customerReference);

        try {
            $this->getQueryBus()->handle(new GetCustomerForEditing($this->customerRegistry[$customerReference]));
            $caughtException = null;
        } catch (Exception $e) {
            $caughtException = $e;
        }

        if ($caughtException === null) {
            throw new Exception(sprintf(
                'The customer "%s" exists.',
                $this->customerRegistry[$customerReference]
            ));
        }
    }

    /**
     * @Then /^if I query customer "(.+)" I should get an error '(.+)'$/
     */
    public function assertQueryReturnsErrormessage($customerReference, $errorMessage)
    {
        $this->assertCustomerReferenceExistsInRegistry($customerReference);

        $queryBus = $this->getQueryBus();
        /* @var EditableCustomer $result */
        try {
            $result = $queryBus->handle(new GetCustomerForEditing($this->customerRegistry[$customerReference]));

            throw new NoExceptionAlthoughExpectedException();
        } catch (\Exception $e) {
            if ($e instanceof NoExceptionAlthoughExpectedException) {
                throw $e;
            }

            $this->latestResult = $e;
        }

        $this->assertGotErrorMessage($errorMessage);
    }

    /**
     * @Then /^I should be returned an error message '(.+)'$/
     */
    public function assertGotErrorMessage($message)
    {
        if (!$this->latestResult instanceof \Exception) {
            throw new Exception('Latest Command did not return an error');
        }

        if ($this->latestResult->getMessage() !== $message) {
            throw new Exception(sprintf("Expected error message '%s', got '%s'", $message, $this->latestResult->getMessage()));
        }

        $this->latestResult = null;
    }

    /**
     * @AfterScenario
     */
    public function assertAllErrorMessagesHaveBeenChecked()
    {
        if ($this->latestResult instanceof \Exception) {
            throw $this->latestResult;
        }
    }

    protected function formatCustomerDataIfNeeded(array $data)
    {
        if (array_key_exists('defaultGroupId', $data)) {
            $data['defaultGroupId'] = $this->validateAndFormatCustomerGroupData($data['defaultGroupId']);
        }

        if (array_key_exists('groupIds', $data)) {
            $groupIds = PrimitiveUtils::castStringArrayIntoArray($data['groupIds']);

            $data['groupIds'] = [];
            foreach ($groupIds as $key => $groupName) {
                $data['groupIds'][$key] = $this->validateAndFormatCustomerGroupData($groupName);
            }
        }

        if (array_key_exists('genderId', $data)) {
            $data['genderId'] = $this->validateAndFormatCustomerGenderData($data['genderId']);
        }

        if (array_key_exists('isEnabled', $data)) {
            $data['isEnabled'] = PrimitiveUtils::castStringBooleanIntoBoolean($data['isEnabled']);
        }
        if (array_key_exists('isPartnerOffersSubscribed', $data)) {
            $data['isPartnerOffersSubscribed'] = PrimitiveUtils::castStringBooleanIntoBoolean($data['isPartnerOffersSubscribed']);
        }
        if (array_key_exists('newsletterSubscribed', $data)) {
            $data['newsletterSubscribed'] = PrimitiveUtils::castStringBooleanIntoBoolean($data['newsletterSubscribed']);
        }
        if (!empty($data['riskId'])) {
            $data['riskId'] = SharedStorage::getStorage()->get($data['riskId']);
        } else {
            $data['riskId'] = 0;
        }

        return $data;
    }

    /**
     * @param string $groupName
     *
     * @return int
     *
     * @throws \Exception
     */
    protected function validateAndFormatCustomerGroupData($groupName)
    {
        /** @var DefaultGroupsProviderInterface $groupProvider */
        $groupProvider = CommonFeatureContext::getContainer()->get('prestashop.adapter.group.provider.default_groups_provider');
        $defaultGroups = $groupProvider->getGroups();

        $isValid = false;
        $groupId = null;
        foreach ($defaultGroups->getGroups() as $group) {
            if ($group->getName() === $groupName) {
                $isValid = true;
                $groupId = $group->getId();
            }
        }

        if (!$isValid) {
            throw new Exception(sprintf('groupId %s does not exist', $groupName));
        }

        return $groupId;
    }

    /**
     * @param string $methodName
     *
     * @throws \Exception
     */
    protected function validateDeleteCustomerMethod(string $methodName)
    {
        $availableMethods = CustomerDeleteMethod::getAvailableMethods();

        if (!in_array($methodName, $availableMethods)) {
            throw new Exception(sprintf('Delete method %s does not exist', $methodName));
        }
    }

    /**
     * @param string $genderName
     *
     * @return int
     *
     * @throws \Exception
     */
    protected function validateAndFormatCustomerGenderData($genderName)
    {
        /** @var FormChoiceProviderInterface $provider */
        $provider = CommonFeatureContext::getContainer()->get('prestashop.adapter.form.choice_provider.gender_by_id_choice_provider');
        $availableGenders = $provider->getChoices();

        $isValid = false;
        $genderId = null;
        foreach ($availableGenders as $gender => $id) {
            if ($gender === $genderName) {
                $isValid = true;
                $genderId = $id;
            }
        }

        if (!$isValid) {
            throw new Exception(sprintf('genderId %s does not exist, available genders are %s', $genderName, implode(', ', array_keys($availableGenders))));
        }

        return $genderId;
    }

    /**
     * @return CommandBusInterface
     */
    protected function getCommandBus()
    {
        return CommonFeatureContext::getContainer()->get('prestashop.core.command_bus');
    }

    /**
     * @return CommandBusInterface
     */
    protected function getQueryBus()
    {
        return CommonFeatureContext::getContainer()->get('prestashop.core.query_bus');
    }

    /**
     * @param string $customerReference
     *
     * @throws \Exception
     */
    protected function assertCustomerReferenceExistsInRegistry($customerReference)
    {
        if (!array_key_exists($customerReference, $this->customerRegistry)) {
            throw new Exception(sprintf('Cannot find customer %s in registry', $customerReference));
        }
    }
}
