<?php
/**
 * 2007-2018 PrestaShop.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
use Symfony\Component\Translation\TranslatorInterface;

/**
 * StarterTheme TODO: B2B fields, Genders, CSRF.
 */
class CustomerFormCore extends AbstractForm
{
    protected $template = 'customer/_partials/customer-form.tpl';

    private $context;
    private $urls;

    private $customerPersister;
    private $guest_allowed;
    private $passwordRequired = true;

    public function __construct(
        Smarty $smarty,
        Context $context,
        TranslatorInterface $translator,
        CustomerFormatter $formatter,
        CustomerPersister $customerPersister,
        array $urls
    ) {
        parent::__construct(
            $smarty,
            $translator,
            $formatter
        );

        $this->context = $context;
        $this->urls = $urls;
        $this->customerPersister = $customerPersister;
    }

    public function setGuestAllowed($guest_allowed = true)
    {
        $this->formatter->setPasswordRequired(!$guest_allowed);
        $this->guest_allowed = $guest_allowed;

        return $this;
    }

    public function setPasswordRequired($passwordRequired)
    {
        $this->passwordRequired = $passwordRequired;

        return $this;
    }

    public function fillFromCustomer(Customer $customer)
    {
        $params = get_object_vars($customer);
        $params['id_customer'] = $customer->id;
        $params['birthday'] = $customer->birthday === '0000-00-00' ? null : Tools::displayDate($customer->birthday);

        return $this->fillWith($params);
    }

    /**
     * @return \Customer
     */
    public function getCustomer()
    {
        $customer = new Customer($this->getValue('id_customer'));

        foreach ($this->formFields as $field) {
            $customerField = $field->getName();
            if ($customerField === 'id_customer') {
                $customerField = 'id';
            }
            if (property_exists($customer, $customerField)) {
                $customer->$customerField = $field->getValue();
            }
        }

        return $customer;
    }

    public function validate()
    {
        $emailField = $this->getField('email');
        $id_customer = Customer::customerExists($emailField->getValue(), true, true);
        $customer = $this->getCustomer();
        if ($id_customer && $id_customer != $customer->id) {
            $emailField->addError($this->translator->trans(
                'The email is already used, please choose another one or sign in', array(), 'Shop.Notifications.Error'
            ));
        }

        // birthday is from input type text..., so we need to convert to a valid date
        $birthdayField = $this->getField('birthday');
        if (!empty($birthdayField)) {
            $birthdayValue = $birthdayField->getValue();
            if (!empty($birthdayValue)) {
                $dateBuilt = DateTime::createFromFormat(Context::getContext()->language->date_format_lite, $birthdayValue);
                if (!empty($dateBuilt)) {
                    $birthdayField->setValue($dateBuilt->format('Y-m-d'));
                }
            }
        }

        $this->validateFieldsLengths();
        $this->validateByModules();

        return parent::validate();
    }

    protected function validateFieldsLengths()
    {
        $this->validateFieldLength('email', 255, $this->getEmailMaxLengthViolationMessage());
        $this->validateFieldLength('firstname', 255, $this->getFirstNameMaxLengthViolationMessage());
        $this->validateFieldLength('lastname', 255, $this->getLastNameMaxLengthViolationMessage());
    }

    /**
     * @param $fieldName
     * @param $maximumLength
     * @param $violationMessage
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
            array('email', 255),
            'Shop.Notifications.Error'
        );
    }

    protected function getFirstNameMaxLengthViolationMessage()
    {
        return $this->translator->trans(
            'The %1$s field is too long (%2$d chars max).',
            array('first name', 255),
            'Shop.Notifications.Error'
        );
    }

    protected function getLastNameMaxLengthViolationMessage()
    {
        return $this->translator->trans(
            'The %1$s field is too long (%2$d chars max).',
            array('last name', 255),
            'Shop.Notifications.Error'
        );
    }

    public function submit()
    {
        if ($this->validate()) {
            $clearTextPassword = $this->getValue('password');
            $newPassword = $this->getValue('new_password');

            $ok = $this->customerPersister->save(
                $this->getCustomer(),
                $clearTextPassword,
                $newPassword,
                $this->passwordRequired
            );

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
        return [
            'action' => $this->action,
            'urls' => $this->urls,
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
        $formFieldsAssociated = array();
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
                $validatedCustomerFormFields = Hook::exec('validateCustomerFormFields', array('fields' => $formFields), $moduleId, true);

                if (is_array($validatedCustomerFormFields)) {
                    array_merge($this->formFields, $validatedCustomerFormFields);
                }
            }
        }
    }
}
