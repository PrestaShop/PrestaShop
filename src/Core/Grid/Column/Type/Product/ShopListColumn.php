<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Column\Type\Product;

use PrestaShop\PrestaShop\Core\Grid\Column\AbstractColumn;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Display a list of shops, the first shop is highlighted as bold and the rest are truncated if
 * max_displayed_characters is set.
 */
final class ShopListColumn extends AbstractColumn
{
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'shop_list';
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver
            ->setRequired([
                'field',
                'ids_field',
                'product_id_field',
            ])
            ->setDefaults([
                'sortable' => false,
                'clickable' => false,
                'max_displayed_characters' => 0,
                'shop_group_id' => null,
            ])
            ->setAllowedTypes('field', 'string')
            ->setAllowedTypes('ids_field', 'string')
            ->setAllowedTypes('product_id_field', 'string')
            ->setAllowedTypes('max_displayed_characters', 'int')
            ->setAllowedTypes('shop_group_id', ['int', 'null'])
        ;
    }
}
