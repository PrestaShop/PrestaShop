<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Customer;
use PrestaShop\PrestaShop\Core\Domain\Customer\Command\SetPrivateNoteAboutCustomerCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Command\SetRequiredFieldsForCustomerCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Query\GetRequiredFieldsForCustomer;
use RuntimeException;

class CustomerFeatureContext extends AbstractDomainFeatureContext
{
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
        /** @var Customer $customer */
        $customer = $this->getSharedStorage()->get($reference);

        $this->getCommandBus()->handle(new SetPrivateNoteAboutCustomerCommand((int) $customer->id, $privateNote));

        $this->getSharedStorage()->set($reference, new Customer($customer->id));
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
}
