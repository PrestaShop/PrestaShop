<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
            return Tools::redirect($context->link->getPageLink('authentication'));
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
        /*
         * This form is tricky - fields may change depending on which country is being selected.
         * Country preselection priority order:
         * 1) Update the format if a new id_country was set.
         * 2) Detect country from address if set
         * 3) Use context country - either a default one or the one geolocated.
         */
        if (isset($params['id_country'])) {
            $country = (int) $params['id_country'] !== (int) $this->formatter->getCountry()->id
                ? new Country($params['id_country'], $this->language->id)
                : $this->formatter->getCountry()
            ;
        } elseif ($this->address) {
            $country = $this->formatter->getCountry();
        } else {
            $country = Context::getContext()->country;
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
            if (property_exists($address, $formField->getName())) {
                $address->{$formField->getName()} = $formField->getValue();
            }
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
