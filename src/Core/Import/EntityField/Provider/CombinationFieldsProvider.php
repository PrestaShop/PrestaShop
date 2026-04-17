<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Import\EntityField\Provider;

use PrestaShop\PrestaShop\Core\Import\EntityField\EntityField;
use PrestaShop\PrestaShop\Core\Import\EntityField\EntityFieldCollection;
use Symfony\Contracts\Translation\TranslatorInterface;

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
            new EntityField('ean13', $this->trans('EAN-13', 'Admin.Advparameters.Feature')),
            new EntityField('isbn', $this->trans('ISBN', 'Admin.Catalog.Feature')),
            new EntityField('upc', $this->trans('UPC', 'Admin.Advparameters.Feature')),
            new EntityField('mpn', $this->trans('MPN', 'Admin.Catalog.Feature')),
            new EntityField('wholesale_price', $this->trans('Cost price', 'Admin.Catalog.Feature')),
            new EntityField('price', $this->trans('Impact on price', 'Admin.Catalog.Feature')),
            new EntityField('ecotax', $this->trans('Ecotax', 'Admin.Catalog.Feature')),
            new EntityField('quantity', $this->trans('Quantity', 'Admin.Global')),
            new EntityField('minimal_quantity', $this->trans('Minimal quantity', 'Admin.Advparameters.Feature')),
            new EntityField('low_stock_threshold', $this->trans('Low stock level', 'Admin.Catalog.Feature')),
            new EntityField(
                'low_stock_alert',
                $this->trans('Receive a low stock alert by email', 'Admin.Catalog.Feature')
            ),
            new EntityField('weight', $this->trans('Impact on weight', 'Admin.Catalog.Feature')),
            new EntityField('default_on', $this->trans('Default (0 = No, 1 = Yes)', 'Admin.Advparameters.Feature')),
            new EntityField('available_date', $this->trans('Combination availability date', 'Admin.Advparameters.Feature')),
            new EntityField('image_position', $this->trans('Choose among product images by position (1,2,3...)', 'Admin.Advparameters.Feature')),
            new EntityField('image_url', $this->trans('Image URLs (x,y,z...)', 'Admin.Advparameters.Feature')),
            new EntityField('image_alt', $this->trans('Image alt texts (x,y,z...)', 'Admin.Advparameters.Feature')),
            new EntityField(
                'shop',
                $this->trans('ID / Name of the store', 'Admin.Advparameters.Feature'),
                $this->trans('Ignore this field if you don\'t use the Multistore tool. If you leave this field empty, the default store will be used.', 'Admin.Advparameters.Help')
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
