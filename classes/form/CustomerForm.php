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
use Symfony\Component\Translation\TranslatorInterface;
use ZxcvbnPhp\Zxcvbn;

/**
 * StarterTheme TODO: B2B fields, Genders, CSRF.
 */
class CustomerFormCore extends AbstractForm
{
    protected $template = 'customer/_partials/customer-form.tpl';

    /**
     * @var CustomerFormatter
     */
    protected $formatter;

    private $context;
    private $urls;

    private $customerPersister;
    private $guest_allowed;
    private $passwordRequired = true;

    private $IDNConverter;

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
        $this->IDNConverter = new InternationalizedDomainNameConverter();
    }

    public function setGuestAllowed($guest_allowed = true)
    {
        $this->formatter->setPasswordRequired(!$guest_allowed);
        $this->setPasswordRequired(!$guest_allowed);
        $this->guest_allowed = $guest_allowed;

        return $this;
    }

    public function setPasswordRequired($passwordRequired)
    {
        $this->passwordRequired = $passwordRequired;

        return $this;
    }

    public function fillWith(array $params = [])
    {
        if (!empty($params['email'])) {
            // In some cases, browsers convert non ASCII chars (from input type="email") to "punycode",
            // we need to convert it back
            $params['email'] = $this->IDNConverter->emailToUtf8($params['email']);
        }

        return parent::fillWith($params);
    }

    public function fillFromCustomer(Customer $customer)
    {
        $params = get_object_vars($customer);
        $params['birthday'] = $customer->birthday === '0000-00-00' ? null : Tools::displayDate($customer->birthday);

        return $this->fillWith($params);
    }

    /**
     * @return \Customer
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
        $emailField = $this->getField('email');
        $id_customer = Customer::customerExists($emailField->getValue(), true, true);
        $customer = $this->getCustomer();
        if ($id_customer && $id_customer != $customer->id) {
            $emailField->addError($this->translator->trans(
                'The email is already used, please choose another one or sign in',
                [],
                'Shop.Notifications.Error'
            ));
        }

        // check birthdayField against null case is mandatory.
        $birthdayField = $this->getField('birthday');
        if (!empty($birthdayField) &&
            !empty($birthdayField->getValue()) &&
            Validate::isBirthDate($birthdayField->getValue(), $this->context->language->date_format_lite)
        ) {
            $dateBuilt = DateTime::createFromFormat(
                $this->context->language->date_format_lite,
                $birthdayField->getValue()
            );
            $birthdayField->setValue($dateBuilt->format('Y-m-d'));
        }

        if ($this->getField('new_password') === null
            || !empty($this->getField('new_password')->getValue())
        ) {
            $passwordField = $this->getField('new_password') ?? $this->getField('password');
            if (!empty($passwordField->getValue()) || $this->passwordRequired) {
                if (Validate::isAcceptablePasswordLength($passwordField->getValue()) === false) {
                    $passwordField->addError($this->translator->trans(
                        'Password must be between %d and %d characters long',
                        [
                            Configuration::get(PasswordPolicyConfiguration::CONFIGURATION_MINIMUM_LENGTH),
                            Configuration::get(PasswordPolicyConfiguration::CONFIGURATION_MAXIMUM_LENGTH),
                        ],
                        'Shop.Notifications.Error'
                    ));
                }

                if (Validate::isAcceptablePasswordScore($passwordField->getValue()) === false) {
                    $wordingsForScore = [
                        $this->translator->trans('Very weak', [], 'Shop.Theme.Global'),
                        $this->translator->trans('Weak', [], 'Shop.Theme.Global'),
                        $this->translator->trans('Average', [], 'Shop.Theme.Global'),
                        $this->translator->trans('Strong', [], 'Shop.Theme.Global'),
                        $this->translator->trans('Very strong', [], 'Shop.Theme.Global'),
                    ];
                    $globalErrorMessage = $this->translator->trans(
                        'The minimum score must be: %s',
                        [
                            $wordingsForScore[(int) Configuration::get(PasswordPolicyConfiguration::CONFIGURATION_MINIMUM_SCORE)],
                        ],
                        'Shop.Notifications.Error'
                    );
                    if ($this->context->shop->theme->get('global_settings.new_password_policy_feature') !== true) {
                        $zxcvbn = new Zxcvbn();
                        $result = $zxcvbn->passwordStrength($passwordField->getValue());
                        if (!empty($result['feedback']['warning'])) {
                            $passwordField->addError($this->translator->trans(
                                $result['feedback']['warning'], [], 'Shop.Theme.Global'
                            ));
                        } else {
                            $passwordField->addError($globalErrorMessage);
                        }
                        foreach ($result['feedback']['suggestions'] as $suggestion) {
                            $passwordField->addError($this->translator->trans($suggestion, [], 'Shop.Theme.Global'));
                        }
                    } else {
                        $passwordField->addError($globalErrorMessage);
                    }
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

    protected function getFirstNameMaxLengthViolationMessage()
    {
        return $this->translator->trans(
            'The %1$s field is too long (%2$d chars max).',
            ['first name', 255],
            'Shop.Notifications.Error'
        );
    }

    protected function getLastNameMaxLengthViolationMessage()
    {
        return $this->translator->trans(
            'The %1$s field is too long (%2$d chars max).',
            ['last name', 255],
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
