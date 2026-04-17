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
 * Display the detail of a shop with its name and associated color.
 */
class ShopNameColumn extends AbstractColumn
{
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'shop_name';
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
                'color_field',
            ])
            ->setDefaults([
                'sortable' => false,
                'clickable' => false,
            ])
            ->setAllowedTypes('field', 'string')
            ->setAllowedTypes('color_field', 'string')
        ;
    }
}
