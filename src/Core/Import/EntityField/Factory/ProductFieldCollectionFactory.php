<?php
/**
 * 2007-2018 PrestaShop
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

namespace PrestaShop\PrestaShop\Core\Import\EntityField\Factory;

use PrestaShop\PrestaShop\Core\Import\EntityField\EntityField;
use PrestaShop\PrestaShop\Core\Import\EntityField\EntityFieldCollection;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class ProductFieldCollectionFactory defines a product field collection factory
 */
final class ProductFieldCollectionFactory implements EntityFieldCollectionFactoryInterface
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
    public function create()
    {
        $fields = [
            new EntityField('id', $this->trans('ID', 'Admin.Global')),
            new EntityField('active', $this->trans('Active (0/1)')),
            new EntityField('name', $this->trans('Name', 'Admin.Global')),
            new EntityField('category', $this->trans('Categories (x,y,z...)')),
            new EntityField('price_tex', $this->trans('Price tax excluded')),
            new EntityField('price_tin', $this->trans('Price tax included')),
            new EntityField('id_tax_rules_group', $this->trans('Tax rule ID')),
            new EntityField('wholesale_price', $this->trans('Cost price', 'Admin.Catalog.Feature')),
            new EntityField('on_sale', $this->trans('On sale (0/1)')),
            new EntityField('reduction_price', $this->trans('Discount amount')),
            new EntityField('reduction_percent', $this->trans('Discount percent')),
            new EntityField('reduction_from', $this->trans('Discount from (yyyy-mm-dd)')),
            new EntityField('reduction_to', $this->trans('Discount to (yyyy-mm-dd)')),
            new EntityField('reference', $this->trans('Reference #')),
            new EntityField('supplier_reference', $this->trans('Supplier reference #')),
            new EntityField('supplier', $this->trans('Supplier', 'Admin.Global')),
            new EntityField('manufacturer', $this->trans('Brand', 'Admin.Global')),
            new EntityField('ean13', $this->trans('EAN13')),
            new EntityField('upc', $this->trans('UPC')),
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
                $this->trans('Delivery time of out-of-stock products with allowed orders:')
            ),
            new EntityField('quantity', $this->trans('Quantity', 'Admin.Global')),
            new EntityField('minimal_quantity', $this->trans('Minimal quantity')),
            new EntityField('low_stock_threshold', $this->trans('Low stock level', 'Admin.Catalog.Feature')),
            new EntityField(
                'low_stock_alert',
                $this->trans('Send me an email when the quantity is under this level', 'Admin.Catalog.Feature')
            ),
            new EntityField('visibility', $this->trans('Visibility', 'Admin.Catalog.Feature')),
            new EntityField('additional_shipping_cost', $this->trans('Additional shipping cost')),
            new EntityField('unity', $this->trans('Unit for the price per unit')),
            new EntityField('unit_price', $this->trans('Price per unit')),
            new EntityField('description_short', $this->trans('Summary', 'Admin.Catalog.Feature')),
            new EntityField('description', $this->trans('Description', 'Admin.Global')),
            new EntityField('tags', $this->trans('Tags (x,y,z...)')),
            new EntityField('meta_title', $this->trans('Meta title', 'Admin.Global')),
            new EntityField('meta_keywords', $this->trans('Meta keywords', 'Admin.Global')),
            new EntityField('meta_description', $this->trans('Meta description', 'Admin.Global')),
            new EntityField('link_rewrite', $this->trans('Rewritten URL')),
            new EntityField('available_now', $this->trans('Label when in stock', 'Admin.Catalog.Feature')),
            new EntityField('available_later', $this->trans('Label when backorder allowed')),
            new EntityField('available_for_order', $this->trans('Available for order (0 = No, 1 = Yes)')),
            new EntityField('available_date', $this->trans('Product availability date')),
            new EntityField('date_add', $this->trans('Product creation date')),
            new EntityField('show_price', $this->trans('Show price (0 = No, 1 = Yes)')),
            new EntityField('image', $this->trans('Image URLs (x,y,z...)')),
            new EntityField('image_alt', $this->trans('Image alt texts (x,y,z...)')),
            new EntityField('delete_existing_images', $this->trans('Delete existing images (0 = No, 1 = Yes)')),
            new EntityField('features', $this->trans('Feature (Name:Value:Position:Customized)')),
            new EntityField('online_only', $this->trans('Available online only (0 = No, 1 = Yes)')),
            new EntityField('condition', $this->trans('Condition', 'Admin.Catalog.Feature')),
            new EntityField('customizable', $this->trans('Customizable (0 = No, 1 = Yes)')),
            new EntityField('uploadable_files', $this->trans('Uploadable files (0 = No, 1 = Yes)')),
            new EntityField('text_fields', $this->trans('Text fields (0 = No, 1 = Yes)')),
            new EntityField('out_of_stock', $this->trans('Action when out of stock')),
            new EntityField('is_virtual', $this->trans('Virtual product (0 = No, 1 = Yes)')),
            new EntityField('file_url', $this->trans('File URL')),
            new EntityField(
                'nb_downloadable',
                $this->trans('Number of allowed downloads', 'Admin.Catalog.Feature'),
                $this->trans(
                    'Number of days this file can be accessed by customers. Set to zero for unlimited access.',
                    'Admin.Catalog.Help'
                )
            ),
            new EntityField('date_expiration', $this->trans('Expiration date (yyyy-mm-dd)')),
            new EntityField(
                'nb_days_accessible',
                $this->trans('Number of days'),
                $this->trans(
                    'Number of days this file can be accessed by customers. Set to zero for unlimited access.',
                    'Admin.Catalog.Help'
                )
            ),
            new EntityField(
                'shop',
                $this->trans('ID / Name of shop'),
                $this->trans(
                    'Ignore this field if you don\'t use the Multistore tool. If you leave this field empty, the default shop will be used.',
                    'Admin.Advparameters.Help'
                )
            ),
            new EntityField(
                'advanced_stock_management',
                $this->trans('Advanced Stock Management'),
                $this->trans(
                    'Enable Advanced Stock Management on product (0 = No, 1 = Yes).',
                    'Admin.Advparameters.Help'
                )
            ),
            new EntityField(
                'depends_on_stock',
                $this->trans('Depends on stock'),
                $this->trans(
                    '0 = Use quantity set in product, 1 = Use quantity from warehouse.',
                    'Admin.Advparameters.Help'
                )
            ),
            new EntityField(
                'warehouse',
                $this->trans('Warehouse'),
                $this->trans(
                    'ID of the warehouse to set as storage.',
                    'Admin.Advparameters.Help'
                )
            ),
            new EntityField('accessories', $this->trans('Accessories (x,y,z...)')),
        ];

        return EntityFieldCollection::createFromArray($fields);
    }

    /**
     * A shorter name method for translations
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
