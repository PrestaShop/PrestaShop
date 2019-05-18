<?php
/**
 * 2007-2019 PrestaShop and Contributors
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

namespace PrestaShop\PrestaShop\Core\Import\EntityField\Provider;

use PrestaShop\PrestaShop\Core\Import\EntityField\EntityField;
use PrestaShop\PrestaShop\Core\Import\EntityField\EntityFieldCollection;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class AddressFieldsProvider defines an address fields provider.
 */
final class AddressFieldsProvider implements EntityFieldsProviderInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function getCollection()
    {
        $fields = [
            new EntityField('id', $this->trans('ID', 'Admin.Global')),
            new EntityField('alias', $this->trans('Alias', 'Admin.Shopparameters.Feature'), '', true),
            new EntityField('active', $this->trans('Active  (0/1)', 'Admin.Advparameters.Feature')),
            new EntityField('customer_email', $this->trans('Customer email', 'Admin.Advparameters.Feature'), '', true),
            new EntityField('id_customer', $this->trans('Customer ID', 'Admin.Advparameters.Feature')),
            new EntityField('manufacturer', $this->trans('Brand', 'Admin.Global')),
            new EntityField('supplier', $this->trans('Supplier', 'Admin.Global')),
            new EntityField('company', $this->trans('Company', 'Admin.Global')),
            new EntityField('lastname', $this->trans('Last name', 'Admin.Global'), '', true),
            new EntityField('firstname', $this->trans('First name ', 'Admin.Global'), '', true),
            new EntityField('address1', $this->trans('Address', 'Admin.Global'), '', true),
            new EntityField('address2', $this->trans('Address (2)', 'Admin.Global')),
            new EntityField('postcode', $this->trans('Zip/postal code', 'Admin.Global'), '', true),
            new EntityField('city', $this->trans('City', 'Admin.Global'), '', true),
            new EntityField('country', $this->trans('Country', 'Admin.Global'), '', true),
            new EntityField('state', $this->trans('State', 'Admin.Global')),
            new EntityField('other', $this->trans('Other', 'Admin.Global')),
            new EntityField('phone', $this->trans('Phone', 'Admin.Global')),
            new EntityField('phone_mobile', $this->trans('Mobile Phone', 'Admin.Global')),
            new EntityField('vat_number', $this->trans('VAT number', 'Admin.Orderscustomers.Feature')),
            new EntityField('dni', $this->trans('Identification number', 'Admin.Orderscustomers.Feature')),
        ];

        return EntityFieldCollection::createFromArray($fields);
    }

    /**
     * A shorter name method for translations.
     *
     * @param string $id translation ID
     * @param string $domain translation domain
     *
     * @return string
     */
    private function trans($id, $domain = 'Admin.Advparameters.Feature')
    {
        return $this->translator->trans($id, [], $domain);
    }
}
