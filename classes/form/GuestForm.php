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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
use PrestaShop\PrestaShop\Core\Security\PasswordPolicyConfiguration;
use PrestaShop\PrestaShop\Core\Util\InternationalizedDomainNameConverter;
use Symfony\Contracts\Translation\TranslatorInterface;
use ZxcvbnPhp\Zxcvbn;

/**
 * StarterTheme TODO: B2B fields, Genders, CSRF.
 */
class GuestFormCore extends AbstractForm
{
    protected $template = 'customer/_partials/guest-form.tpl';

    /**
     * @var CustomerFormatter
     */
    protected $formatter;

    private $context;

    private $customerPersister;

    private $IDNConverter;

    public function __construct(
        Smarty $smarty,
        Context $context,
        TranslatorInterface $translator,
        GuestFormatter $formatter,
        CustomerPersister $customerPersister,
    ) {
        parent::__construct(
            $smarty,
            $translator,
            $formatter
        );

        $this->customerPersister = $customerPersister;
        $this->context = $context;
        $this->IDNConverter = new InternationalizedDomainNameConverter();
        if (!$this->formFields) {
            $this->formFields = $this->formatter->getFormat();
        }
    }

    public function fillWith(array $params = [])
    {
        if (!empty($params['email'])) {
            $params['email'] = $this->IDNConverter->emailToUtf8($params['email']);
        }

        return parent::fillWith($params);
    }

    public function fillFromCustomer(Customer $customer)
    {
        $params = get_object_vars($customer);

        return $this->fillWith($params);
    }

    /**
     * @return Customer
     */
    public function getCustomer()
    {
        $customer = new Customer($this->context->customer->id);

        foreach ($this->formFields as $field) {
            $customerField = $field->getName();
            if (property_exists($customer, $customerField)) {
                $customer->$customerField = $field->getValue();
            }
        }

        return $customer;
    }

    public function validate()
    {
        $this->validateFieldsLengths();
        $this->validateByModules();

        return parent::validate();
    }

    protected function validateFieldsLengths()
    {
        $this->validateFieldLength('email', 255, $this->getEmailMaxLengthViolationMessage());
    }

    /**
     * @param string $fieldName
     * @param int $maximumLength
     * @param string $violationMessage
     */
    protected function validateFieldLength($fieldName, $maximumLength, $violationMessage)
    {
        $emailField = $this->getField($fieldName);
        if (strlen($emailField->getValue()) > $maximumLength) {
            $emailField->addError($violationMessage);
        }
    }

    /**
     * @return mixed
     */
    protected function getEmailMaxLengthViolationMessage()
    {
        return $this->translator->trans(
            'The %1$s field is too long (%2$d chars max).',
            ['email', 255],
            'Shop.Notifications.Error'
        );
    }

    public function submit()
    {
        if ($this->validate()) {

            $ok = $this->customerPersister->save($this->getCustomer());

            if (!$ok) {
                foreach ($this->customerPersister->getErrors() as $field => $errors) {
                    $this->formFields[$field]->setErrors($errors);
                }
            }

            return $ok;
        }

        return false;
    }

    public function getTemplateVariables()
    {
        if (!$this->formFields) {
            $this->formFields = $this->formatter->getFormat();
        }

        return [
            'action' => $this->action,
            'errors' => $this->getErrors(),
            'hook_create_account_form' => Hook::exec('displayCustomerAccountForm'),
            'formFields' => array_map(
                function (FormField $field) {
                    return $field->toArray();
                },
                $this->formFields
            ),
        ];
    }

    /**
     * This function call the hook validateCustomerFormFields of every modules
     * which added one or several fields to the customer registration form.
     *
     * Note: they won't get all the fields from the form, but only the one
     * they added.
     */
    private function validateByModules()
    {
        $formFieldsAssociated = [];
        // Group FormField instances by module name
        foreach ($this->formFields as $formField) {
            if (!empty($formField->moduleName)) {
                $formFieldsAssociated[$formField->moduleName][] = $formField;
            }
        }
        // Because of security reasons (i.e password), we don't send all
        // the values to the module but only the ones it created
        foreach ($formFieldsAssociated as $moduleName => $formFields) {
            if ($moduleId = Module::getModuleIdByName($moduleName)) {
                // ToDo : replace Hook::exec with HookFinder, because we expect a specific class here
                // Hook called only for the module concerned
                // An array [module_name => module_output] will be returned
                $validatedCustomerFormFields = Hook::exec('validateCustomerFormFields', ['fields' => $formFields], $moduleId, true);

                if (!is_array($validatedCustomerFormFields)) {
                    continue;
                }

                foreach ($validatedCustomerFormFields as $name => $field) {
                    if ($field instanceof FormFieldCore) {
                        $this->formFields[$name] = $field;
                    }
                }
            }
        }
    }
}
