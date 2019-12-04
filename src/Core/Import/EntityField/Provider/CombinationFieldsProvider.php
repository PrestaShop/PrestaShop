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

namespace PrestaShop\PrestaShop\Core\Import\EntityField\Provider;

use PrestaShop\PrestaShop\Core\Import\EntityField\EntityField;
use PrestaShop\PrestaShop\Core\Import\EntityField\EntityFieldCollection;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class CombinationFieldsProvider defines a combination fields provider.
 */
final class CombinationFieldsProvider implements EntityFieldsProviderInterface
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
            new EntityField('id_product', $this->trans('Product ID', 'Admin.Advparameters.Feature')),
            new EntityField('product_reference', $this->trans('Product reference', 'Admin.Advparameters.Feature')),
            new EntityField('group', $this->trans('Attribute (Name:Type:Position)', 'Admin.Advparameters.Feature'), '', true),
            new EntityField('attribute', $this->trans('Value (Value:Position)', 'Admin.Advparameters.Feature'), '', true),
            new EntityField('supplier_reference', $this->trans('Supplier reference', 'Admin.Advparameters.Feature')),
            new EntityField('reference', $this->trans('Reference', 'Admin.Global')),
            new EntityField('ean13', $this->trans('EAN13', 'Admin.Advparameters.Feature')),
            new EntityField('upc', $this->trans('UPC', 'Admin.Advparameters.Feature')),
            new EntityField('mpn', $this->trans('MPN', 'Admin.Advparameters.Feature')),
            new EntityField('wholesale_price', $this->trans('Cost price', 'Admin.Catalog.Feature')),
            new EntityField('price', $this->trans('Impact on price', 'Admin.Catalog.Feature')),
            new EntityField('ecotax', $this->trans('Ecotax', 'Admin.Catalog.Feature')),
            new EntityField('quantity', $this->trans('Quantity', 'Admin.Global')),
            new EntityField('minimal_quantity', $this->trans('Minimal quantity', 'Admin.Advparameters.Feature')),
            new EntityField('low_stock_threshold', $this->trans('Low stock level', 'Admin.Catalog.Feature')),
            new EntityField(
                'low_stock_alert',
                $this->trans('Send me an email when the quantity is under this level', 'Admin.Catalog.Feature')
            ),
            new EntityField('weight', $this->trans('Impact on weight', 'Admin.Catalog.Feature')),
            new EntityField('default_on', $this->trans('Default (0 = No, 1 = Yes)', 'Admin.Advparameters.Feature')),
            new EntityField('available_date', $this->trans('Combination availability date', 'Admin.Advparameters.Feature')),
            new EntityField('image_position', $this->trans('Choose among product images by position (1,2,3...)', 'Admin.Advparameters.Feature')),
            new EntityField('image_url', $this->trans('Image URLs (x,y,z...)', 'Admin.Advparameters.Feature')),
            new EntityField('image_alt', $this->trans('Image alt texts (x,y,z...)', 'Admin.Advparameters.Feature')),
            new EntityField(
                'shop',
                $this->trans('ID / Name of shop', 'Admin.Advparameters.Feature'),
                $this->trans('Ignore this field if you don\'t use the Multistore tool. If you leave this field empty, the default shop will be used.', 'Admin.Advparameters.Help')
            ),
            new EntityField(
                'advanced_stock_management',
                $this->trans('Advanced Stock Management', 'Admin.Advparameters.Feature'),
                $this->trans('Enable Advanced Stock Management on product (0 = No, 1 = Yes)', 'Admin.Advparameters.Help')
            ),
            new EntityField(
                'depends_on_stock',
                $this->trans('Depends on stock', 'Admin.Advparameters.Feature'),
                $this->trans('0 = Use quantity set in product, 1 = Use quantity from warehouse.', 'Admin.Advparameters.Help')
            ),
            new EntityField(
                'warehouse',
                $this->trans('Warehouse', 'Admin.Advparameters.Feature'),
                $this->trans('ID of the warehouse to set as storage.', 'Admin.Advparameters.Help')
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
