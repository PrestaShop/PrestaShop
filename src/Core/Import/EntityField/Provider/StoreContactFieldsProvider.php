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
 * Class StoreContactFieldCollectionFactory defines a store contact fields provider.
 */
final class StoreContactFieldsProvider implements EntityFieldsProviderInterface
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
            new EntityField('active', $this->trans('Active (0/1)', 'Admin.Advparameters.Feature')),
            new EntityField('name', $this->trans('Name', 'Admin.Global')),
            new EntityField('address1', $this->trans('Address', 'Admin.Global'), '', true),
            new EntityField('address2', $this->trans('Address (2)', 'Admin.Advparameters.Feature')),
            new EntityField('postcode', $this->trans('Zip/postal code', 'Admin.Global')),
            new EntityField('state', $this->trans('State', 'Admin.Global')),
            new EntityField('city', $this->trans('City', 'Admin.Global'), '', true),
            new EntityField('country', $this->trans('Country', 'Admin.Global'), '', true),
            new EntityField('latitude', $this->trans('Latitude', 'Admin.Advparameters.Feature'), '', true),
            new EntityField('longitude', $this->trans('Longitude', 'Admin.Advparameters.Feature'), '', true),
            new EntityField('phone', $this->trans('Phone', 'Admin.Global')),
            new EntityField('fax', $this->trans('Fax', 'Admin.Global')),
            new EntityField('email', $this->trans('Email address', 'Admin.Global')),
            new EntityField('note', $this->trans('Note', 'Admin.Global')),
            new EntityField('hours', $this->trans('Hours (x,y,z...)', 'Admin.Advparameters.Feature')),
            new EntityField('image', $this->trans('Image URL', 'Admin.Advparameters.Feature')),
            new EntityField(
                'shop',
                $this->trans('ID / Name of shop', 'Admin.Advparameters.Feature'),
                $this->trans('Ignore this field if you don\'t use the Multistore tool. If you leave this field empty, the default shop will be used.', 'Admin.Advparameters.Help')
            ),
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
