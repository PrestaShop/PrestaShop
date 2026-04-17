<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\Category;

use PrestaShop\PrestaShop\Core\Grid\Action\Row\AbstractRowAction;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class DeleteCategoryRowAction adds "Delete" action to row.
 */
final class DeleteCategoryRowAction extends AbstractRowAction
{
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'delete_category';
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver
            ->setRequired([
                'category_id_field',
                'category_delete_route',
            ])
            ->setAllowedTypes('category_id_field', 'string')
            ->setAllowedTypes('category_delete_route', 'string');
    }
}
