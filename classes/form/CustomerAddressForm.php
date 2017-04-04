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
        $this->address = new Address($id_address, $this->language->id);

        $params = get_object_vars($this->address);
        $params['id_address'] = $this->address->id;

        return $this->fillWith($params);
    }

    public function fillWith(array $params = [])
    {
        // This form is very tricky: fields may change depending on which
        // country is being submitted!
        // So we first update the format if a new id_country was set.
        if (isset($params['id_country'])
            && $params['id_country'] != $this->formatter->getCountry()->id
        ) {
            $this->formatter->setCountry(new Country(
                $params['id_country'],
                $this->language->id
            ));
        }

        return parent::fillWith($params);
    }

    public function validate()
    {
        $is_valid = parent::validate();

        if (($postcode = $this->getField('postcode'))) {
            if ($postcode->isRequired()) {
                $country = $this->formatter->getCountry();
                if (!$country->checkZipCode($postcode->getValue())) {
                    // FIXME: the translator adapter is crap at the moment,
                    // but once it is not, the sprintf needs to go away.
                    $postcode->addError(sprintf(
                        $this->translator->trans(
                            'Invalid postcode - should look like "%1$s"', [], 'Shop.Forms.Errors'
                        ),
                        $country->zip_code_format
                    ));
                    $is_valid = false;
                }
            }
        }

        if (($hookReturn = Hook::exec('actionValidateCustomerAddressForm', array('form' => $this))) != '') {
            $is_valid &= (bool) $hookReturn;
        }

        return $is_valid;
    }

    public function submit()
    {
        if (!$this->validate()) {
            return false;
        }

        $address = new Address(
            $this->getValue('id_address'),
            $this->language->id
        );

        foreach ($this->formFields as $formField) {
            $address->{$formField->getName()} = $formField->getValue();
        }

        if (empty($address->alias)) {
            $address->alias = $this->translator->trans('My Address', [], 'Shop.Theme.Checkout');
        }

        $this->address = $address;

        return $this->persister->save(
            $this->address,
            $this->getValue('token')
        );
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function getTemplateVariables()
    {
        if (!$this->formFields) {
            // This is usually done by fillWith but the form may be
            // rendered before fillWith is called.
            // I don't want to assign formFields in the constructor
            // because it accesses the DB and a constructor should not
            // have side effects.
            $this->formFields = $this->formatter->getFormat();
        }

        $this->setValue('token', $this->persister->getToken());

        return array(
            'id_address' => (isset($this->address->id)) ? $this->address->id : 0,
            'action' => $this->action,
            'errors' => $this->getErrors(),
            'formFields' => array_map(
                function (FormField $item) {
                    return $item->toArray();
                },
                $this->formFields
            ),
        );
    }
}
