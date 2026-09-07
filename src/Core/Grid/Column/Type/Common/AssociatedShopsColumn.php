<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Column\Type\Common;

use PrestaShop\PrestaShop\Core\Grid\Column\AbstractColumn;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Displays the list of shops a record is associated with (multistore grids).
 *
 * Purely presentational: expects the record to already carry the shop names ($field,
 * a list of strings), typically resolved in one batched query by a grid data factory
 * decorator. An empty list renders the $empty_label instead (e.g. "All stores" for
 * records associated with every shop).
 *
 * Unlike the product grid's shop_list column, this one has no expandable per-shop
 * preview — the product preview exists because product DATA differs per shop; a plain
 * association has nothing more to show than the list itself.
 */
final class AssociatedShopsColumn extends AbstractColumn
{
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'associated_shops';
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
            ])
            ->setDefaults([
                'sortable' => false,
                'clickable' => false,
                'max_displayed_characters' => 0,
                'empty_label' => '',
            ])
            ->setAllowedTypes('field', 'string')
            ->setAllowedTypes('max_displayed_characters', 'int')
            ->setAllowedTypes('empty_label', 'string')
        ;
    }
}
