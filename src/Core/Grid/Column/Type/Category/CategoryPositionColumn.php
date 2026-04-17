<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Column\Type\Category;

use PrestaShop\PrestaShop\Core\Grid\Column\AbstractColumn;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CategoryPositionColumn.
 */
final class CategoryPositionColumn extends AbstractColumn
{
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'category_position';
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired([
                'field',
                'id_field',
                'id_parent_field',
                'update_route',
            ])
            ->setDefaults([
                'sortable' => true,
            ])
            ->setAllowedTypes('sortable', 'bool')
            ->setAllowedTypes('field', 'string')
            ->setAllowedTypes('id_field', 'string')
            ->setAllowedTypes('id_parent_field', 'string')
            ->setAllowedTypes('update_route', 'string');
    }
}
