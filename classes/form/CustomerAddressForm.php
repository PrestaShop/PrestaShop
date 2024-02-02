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
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * StarterTheme TODO: FIXME:
 * In the old days, when updating an address, we actually:
 * - checked if the address was used by an order
 * - if so, just mark it as deleted and create a new one
 * - otherwise, update it like a normal entity
 * I *think* this is not necessary now because the invoicing thing
 * does its own historization. But this should be checked more thoroughly.
 */
class CustomerAddressFormCore extends AbstractForm
{
    private $language;

    protected $template = 'customer/_partials/address-form.tpl';

    /**
     * @var CustomerAddressFormatter
     */
    protected $formatter;

    private $address;

    private $persister;

    public function __construct(
        Smarty $smarty,
        Language $language,
        TranslatorInterface $translator,
        CustomerAddressPersister $persister,
        CustomerAddressFormatter $formatter
    ) {
        parent::__construct(
            $smarty,
            $translator,
            $formatter
        );

        $this->language = $language;
        $this->persister = $persister;
    }

    public function loadAddressById($id_address)
    {
        $context = Context::getContext();

        $this->address = new Address($id_address, $this->language->id);

        if ($this->address->id === null) {
            return Tools::redirect('pagenotfound');
        }

        if (!$context->customer->isLogged() && !$context->customer->isGuest()) {
            return Tools::redirect('/index.php?controller=authentication');
        }

        if ($this->address->id_customer != $context->customer->id) {
            return Tools::redirect('pagenotfound');
        }

        $params = get_object_vars($this->address);
        $params['id_address'] = $this->address->id;

        return $this->fillWith($params);
    }

    public function fillWith(array $params = [])
    {
        // This form is tricky: fields may change depending on which country is being selected!
        // Country preselection priority order :
        // 1) Update the format if a new id_country was set.
        // 2) Detect country from address if set
        // 3) Detect country from browser language settings and matches BO enabled countries
        // 4) Default country set in BO

        if (isset($params['id_country'])) {
            $country = (int) $params['id_country'] !== (int) $this->formatter->getCountry()->id
                ? new Country($params['id_country'], $this->language->id)
                : $this->formatter->getCountry()
            ;
        } elseif ($this->address) {
            $country = $this->formatter->getCountry();
        } elseif (
            Tools::isCountryFromBrowserAvailable() &&
            Country::getByIso($countryIsoCode = Tools::getCountryIsoCodeFromHeader(), true)
        ) {
            $country = new Country((int) Country::getByIso($countryIsoCode, true), Language::getIdByIso($countryIsoCode));
        } else {
            $country = new Country((int) Configuration::get('PS_COUNTRY_DEFAULT'), $this->language->id);
        }

        $this->formatter->setCountry($country);

        return parent::fillWith($params);
    }

    public function validate()
    {
        $is_valid = true;

        $postcode = $this->getField('postcode');
        if ($postcode && $postcode->isRequired()) {
            $country = $this->formatter->getCountry();
            if (!$country->checkZipCode($postcode->getValue())) {
                $postcode->addError($this->translator->trans(
                    'Invalid postcode - should look like "%zipcode%"',
                    ['%zipcode%' => $country->zip_code_format],
                    'Shop.Forms.Errors'
               ));
                $is_valid = false;
            }
        }

        if ($is_valid && Hook::exec('actionValidateCustomerAddressForm', ['form' => $this]) === false) {
            $is_valid = false;
        }

        return $is_valid && parent::validate();
    }

    public function submit()
    {
        if (!$this->validate()) {
            return false;
        }

        $address = new Address(
            Tools::getValue('id_address'),
            $this->language->id
        );

        foreach ($this->formFields as $formField) {
            $address->{$formField->getName()} = $formField->getValue();
        }

        if (!isset($this->formFields['id_state'])) {
            $address->id_state = 0;
        }

        if (empty($address->alias)) {
            $address->alias = $this->translator->trans('My Address', [], 'Shop.Theme.Checkout');
        }

        Hook::exec('actionSubmitCustomerAddressForm', ['address' => &$address]);

        $this->setAddress($address);

        return $this->getPersister()->save(
            $address,
            $this->getValue('token')
        );
    }

    /**
     * @return Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @return CustomerAddressPersister
     */
    protected function getPersister()
    {
        return $this->persister;
    }

    protected function setAddress(Address $address)
    {
        $this->address = $address;
    }

    public function getTemplateVariables()
    {
        $context = Context::getContext();

        if (!$this->formFields) {
            // This is usually done by fillWith but the form may be
            // rendered before fillWith is called.
            // I don't want to assign formFields in the constructor
            // because it accesses the DB and a constructor should not
            // have side effects.
            $this->formFields = $this->formatter->getFormat();
        }

        $this->setValue('token', $this->persister->getToken());
        $formFields = array_map(
            function (FormField $item) {
                return $item->toArray();
            },
            $this->formFields
        );

        if (empty($formFields['firstname']['value'])) {
            $formFields['firstname']['value'] = $context->customer->firstname;
        }

        if (empty($formFields['lastname']['value'])) {
            $formFields['lastname']['value'] = $context->customer->lastname;
        }

        return [
            'id_address' => (isset($this->address->id)) ? $this->address->id : 0,
            'action' => $this->action,
            'errors' => $this->getErrors(),
            'formFields' => $formFields,
        ];
    }
}
