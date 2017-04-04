<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

use Symfony\Component\Translation\TranslatorInterface;

class CustomerFormatterCore implements FormFormatterInterface
{
    private $translator;
    private $language;

    private $ask_for_birthdate              = true;
    private $ask_for_partner_optin          = true;
    private $partner_optin_is_required      = true;
    private $ask_for_password               = true;
    private $password_is_required           = true;
    private $ask_for_new_password           = false;

    public function __construct(
        TranslatorInterface $translator,
        Language $language
    ) {
        $this->translator = $translator;
        $this->language = $language;
    }

    public function setAskForBirthdate($ask_for_birthdate)
    {
        $this->ask_for_birthdate = $ask_for_birthdate;
        return $this;
    }

    public function setAskForPartnerOptin($ask_for_partner_optin)
    {
        $this->ask_for_partner_optin = $ask_for_partner_optin;
        return $this;
    }

    public function setPartnerOptinRequired($partner_optin_is_required)
    {
        $this->partner_optin_is_required = $partner_optin_is_required;
        return $this;
    }

    public function setAskForPassword($ask_for_password)
    {
        $this->ask_for_password = $ask_for_password;
        return $this;
    }

    public function setAskForNewPassword($ask_for_new_password)
    {
        $this->ask_for_new_password = $ask_for_new_password;
        return $this;
    }

    public function setPasswordRequired($password_is_required)
    {
        $this->password_is_required = $password_is_required;
        return $this;
    }

    public function getFormat()
    {
        $format = [];

        $format['id_customer'] = (new FormField)
            ->setName('id_customer')
            ->setType('hidden')
        ;

        $genderField = (new FormField)
            ->setName('id_gender')
            ->setType('radio-buttons')
            ->setLabel(
                $this->translator->trans(
                    'Social title', [], 'Shop.Forms.Labels'
                )
            )
        ;
        foreach (Gender::getGenders($this->language->id) as $gender) {
            $genderField->addAvailableValue($gender->id, $gender->name);
        }
        $format[$genderField->getName()] = $genderField;

        $format['firstname'] = (new FormField)
            ->setName('firstname')
            ->setLabel(
                $this->translator->trans(
                    'First name', [], 'Shop.Forms.Labels'
                )
            )
            ->setRequired(true)
        ;

        $format['lastname'] = (new FormField)
            ->setName('lastname')
            ->setLabel(
                $this->translator->trans(
                    'Last name', [], 'Shop.Forms.Labels'
                )
            )
            ->setRequired(true)
        ;

        if (Configuration::get('PS_B2B_ENABLE')) {
            $format['company'] = (new FormField)
                ->setName('company')
                ->setType('text')
                ->setLabel($this->translator->trans(
                    'Company', [], 'Shop.Forms.Labels'
                ));
            $format['siret'] = (new FormField)
                ->setName('siret')
                ->setType('text')
                ->setLabel($this->translator->trans(
                    // Please localize this string with the applicable registration number type in your country. For example : "SIRET" in France and "Código fiscal" in Spain.
                    'Identification number', [], 'Shop.Forms.Labels'
                ));
        }

        $format['email'] = (new FormField)
            ->setName('email')
            ->setType('email')
            ->setLabel(
                $this->translator->trans(
                    'Email', [], 'Shop.Forms.Labels'
                )
            )
            ->setRequired(true)
        ;

        if ($this->ask_for_password) {
            $format['password'] = (new FormField)
                ->setName('password')
                ->setType('password')
                ->setLabel(
                    $this->translator->trans(
                        'Password', [], 'Shop.Forms.Labels'
                    )
                )
                ->setRequired($this->password_is_required)
            ;
        }

        if ($this->ask_for_new_password) {
            $format['new_password'] = (new FormField)
                ->setName('new_password')
                ->setType('password')
                ->setLabel(
                    $this->translator->trans(
                        'New password', [], 'Shop.Forms.Labels'
                    )
                )
            ;
        }

        if ($this->ask_for_birthdate) {
            $format['birthday'] = (new FormField)
                ->setName('birthday')
                ->setType('text')
                ->setLabel(
                    $this->translator->trans(
                        'Birthdate', [], 'Shop.Forms.Labels'
                    )
                )
                ->addAvailableValue('placeholder', Tools::getDateFormat())
                ->addAvailableValue(
                    'comment',
                    $this->translator->trans('(E.g.: %date_format%)', array('%date_format%' => Tools::formatDateStr('31 May 1970')), 'Shop.Forms.Help')
                )
            ;
        }

        if ($this->ask_for_partner_optin) {
            $format['optin'] = (new FormField)
                ->setName('optin')
                ->setType('checkbox')
                ->setLabel(
                    $this->translator->trans(
                        'Receive offers from our partners', [], 'Shop.Theme.Customeraccount'
                    )
                )
                ->setRequired($this->partner_optin_is_required)
            ;
        }

        // ToDo, replace the hook exec with HookFinder when the associated PR will be merged
        $additionalCustomerFormFields = Hook::exec('additionalCustomerFormFields', array(), null, true);

        if (is_array($additionalCustomerFormFields)) {
            foreach ($additionalCustomerFormFields as $moduleName => $additionnalFormFields) {
                foreach ($additionnalFormFields as $formField) {
                    $formField->moduleName = $moduleName;
                    $format[$moduleName.'_'.$formField->getName()] = $formField;
                }
            }
        }

        // TODO: TVA etc.?

        return $this->addConstraints($format);
    }

    private function addConstraints(array $format)
    {
        $constraints = Customer::$definition['fields'];

        foreach ($format as $field) {
            if (!empty($constraints[$field->getName()]['validate'])) {
                $field->addConstraint(
                    $constraints[$field->getName()]['validate']
                );
            }
        }

        return $format;
    }
}
