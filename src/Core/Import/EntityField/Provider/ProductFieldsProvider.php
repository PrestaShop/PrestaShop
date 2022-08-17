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

namespace PrestaShop\PrestaShop\Core\Import\EntityField\Provider;

use PrestaShop\PrestaShop\Core\Import\EntityField\EntityField;
use PrestaShop\PrestaShop\Core\Import\EntityField\EntityFieldCollection;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class ProductFieldsProvider defines a product fields provider.
 */
final class ProductFieldsProvider implements EntityFieldsProviderInterface
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
            new EntityField('name', $this->trans('Name', 'Admin.Global'), '', true),
            new EntityField('category', $this->trans('Categories (x,y,z...)', 'Admin.Advparameters.Feature')),
            new EntityField('price_tex', $this->trans('Price tax excluded', 'Admin.Advparameters.Feature')),
            new EntityField('price_tin', $this->trans('Price tax included', 'Admin.Advparameters.Feature')),
            new EntityField('id_tax_rules_group', $this->trans('Tax rule ID', 'Admin.Advparameters.Feature')),
            new EntityField('wholesale_price', $this->trans('Cost price', 'Admin.Catalog.Feature')),
            new EntityField('on_sale', $this->trans('On sale (0/1)', 'Admin.Advparameters.Feature')),
            new EntityField('reduction_price', $this->trans('Discount amount', 'Admin.Advparameters.Feature')),
            new EntityField('reduction_percent', $this->trans('Discount percent', 'Admin.Advparameters.Feature')),
            new EntityField('reduction_from', $this->trans('Discount from (yyyy-mm-dd)', 'Admin.Advparameters.Feature')),
            new EntityField('reduction_to', $this->trans('Discount to (yyyy-mm-dd)', 'Admin.Advparameters.Feature')),
            new EntityField('reference', $this->trans('Reference #', 'Admin.Advparameters.Feature')),
            new EntityField('supplier_reference', $this->trans('Supplier reference #', 'Admin.Advparameters.Feature')),
            new EntityField('supplier', $this->trans('Supplier', 'Admin.Global')),
            new EntityField('manufacturer', $this->trans('Brand', 'Admin.Global')),
            new EntityField('ean13', $this->trans('EAN13', 'Admin.Advparameters.Feature')),
            new EntityField('upc', $this->trans('UPC', 'Admin.Advparameters.Feature')),
            new EntityField('mpn', $this->trans('MPN', 'Admin.Catalog.Feature')),
            new EntityField('ecotax', $this->trans('Ecotax', 'Admin.Catalog.Feature')),
            new EntityField('width', $this->trans('Width', 'Admin.Global')),
            new EntityField('height', $this->trans('Height', 'Admin.Global')),
            new EntityField('depth', $this->trans('Depth', 'Admin.Global')),
            new EntityField('weight', $this->trans('Weight', 'Admin.Global')),
            new EntityField(
                'delivery_in_stock',
                $this->trans('Delivery time of in-stock products:', 'Admin.Catalog.Feature')
            ),
            new EntityField(
                'delivery_out_stock',
                $this->trans('Delivery time of out-of-stock products with allowed orders:', 'Admin.Advparameters.Feature')
            ),
            new EntityField('quantity', $this->trans('Quantity', 'Admin.Global')),
            new EntityField('minimal_quantity', $this->trans('Minimal quantity', 'Admin.Advparameters.Feature')),
            new EntityField('low_stock_threshold', $this->trans('Low stock level', 'Admin.Catalog.Feature')),
            new EntityField(
                'low_stock_alert',
                $this->trans('Receive a low stock alert by email', 'Admin.Catalog.Feature')
            ),
            new EntityField('visibility', $this->trans('Visibility', 'Admin.Catalog.Feature')),
            new EntityField('additional_shipping_cost', $this->trans('Additional shipping cost', 'Admin.Advparameters.Feature')),
            new EntityField('unity', $this->trans('Unit for the price per unit', 'Admin.Advparameters.Feature')),
            new EntityField('unit_price', $this->trans('Price per unit', 'Admin.Advparameters.Feature')),
            new EntityField('description_short', $this->trans('Summary', 'Admin.Catalog.Feature')),
            new EntityField('description', $this->trans('Description', 'Admin.Global')),
            new EntityField('tags', $this->trans('Tags (x,y,z...)', 'Admin.Advparameters.Feature')),
            new EntityField('meta_title', $this->trans('Meta title', 'Admin.Global')),
            new EntityField('meta_keywords', $this->trans('Meta keywords', 'Admin.Global')),
            new EntityField('meta_description', $this->trans('Meta description', 'Admin.Global')),
            new EntityField('link_rewrite', $this->trans('Rewritten URL', 'Admin.Advparameters.Feature')),
            new EntityField('available_now', $this->trans('Label when in stock', 'Admin.Catalog.Feature')),
            new EntityField('available_later', $this->trans('Label when backorder allowed', 'Admin.Advparameters.Feature')),
            new EntityField('available_for_order', $this->trans('Available for order (0 = No, 1 = Yes)', 'Admin.Advparameters.Feature')),
            new EntityField('available_date', $this->trans('Product availability date', 'Admin.Advparameters.Feature')),
            new EntityField('date_add', $this->trans('Product creation date', 'Admin.Advparameters.Feature')),
            new EntityField('show_price', $this->trans('Show price (0 = No, 1 = Yes)', 'Admin.Advparameters.Feature')),
            new EntityField('image', $this->trans('Image URLs (x,y,z...)', 'Admin.Advparameters.Feature')),
            new EntityField('image_alt', $this->trans('Image alt texts (x,y,z...)', 'Admin.Advparameters.Feature')),
            new EntityField('delete_existing_images', $this->trans('Delete existing images (0 = No, 1 = Yes)', 'Admin.Advparameters.Feature')),
            new EntityField('features', $this->trans('Feature (Name:Value:Position:Customized)', 'Admin.Advparameters.Feature')),
            new EntityField('online_only', $this->trans('Available online only (0 = No, 1 = Yes)', 'Admin.Advparameters.Feature')),
            new EntityField('condition', $this->trans('Condition', 'Admin.Catalog.Feature')),
            new EntityField('customizable', $this->trans('Customizable (0 = No, 1 = Yes)', 'Admin.Advparameters.Feature')),
            new EntityField('uploadable_files', $this->trans('Uploadable files (0 = No, 1 = Yes)', 'Admin.Advparameters.Feature')),
            new EntityField('text_fields', $this->trans('Text fields (0 = No, 1 = Yes)', 'Admin.Advparameters.Feature')),
            new EntityField('out_of_stock', $this->trans('Action when out of stock', 'Admin.Advparameters.Feature')),
            new EntityField('is_virtual', $this->trans('Virtual product (0 = No, 1 = Yes)', 'Admin.Advparameters.Feature')),
            new EntityField('file_url', $this->trans('File URL', 'Admin.Advparameters.Feature')),
            new EntityField(
                'nb_downloadable',
                $this->trans('Number of allowed downloads', 'Admin.Catalog.Feature'),
                $this->trans(
                    'Number of days this file can be accessed by customers. Set to zero for unlimited access.',
                    'Admin.Catalog.Help'
                )
            ),
            new EntityField('date_expiration', $this->trans('Expiration date (yyyy-mm-dd)', 'Admin.Advparameters.Feature')),
            new EntityField(
                'nb_days_accessible',
                $this->trans('Number of days', 'Admin.Advparameters.Feature'),
                $this->trans(
                    'Number of days this file can be accessed by customers. Set to zero for unlimited access.',
                    'Admin.Catalog.Help'
                )
            ),
            new EntityField(
                'shop',
                $this->trans('ID / Name of shop', 'Admin.Advparameters.Feature'),
                $this->trans(
                    'Ignore this field if you don\'t use the Multistore tool. If you leave this field empty, the default shop will be used.',
                    'Admin.Advparameters.Help'
                )
            ),
            new EntityField(
                'advanced_stock_management',
                $this->trans('Advanced Stock Management', 'Admin.Advparameters.Feature'),
                $this->trans(
                    'Enable Advanced Stock Management on product (0 = No, 1 = Yes).',
                    'Admin.Advparameters.Help'
                )
            ),
            new EntityField(
                'depends_on_stock',
                $this->trans('Depends on stock', 'Admin.Advparameters.Feature'),
                $this->trans(
                    '0 = Use quantity set in product, 1 = Use quantity from warehouse.',
                    'Admin.Advparameters.Help'
                )
            ),
            new EntityField(
                'warehouse',
                $this->trans('Warehouse', 'Admin.Advparameters.Feature'),
                $this->trans(
                    'ID of the warehouse to set as storage.',
                    'Admin.Advparameters.Help'
                )
            ),
            new EntityField('accessories', $this->trans('Accessories (x,y,z...)', 'Admin.Advparameters.Feature')),
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
