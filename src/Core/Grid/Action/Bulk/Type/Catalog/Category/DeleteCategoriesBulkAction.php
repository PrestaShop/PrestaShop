<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Action\Bulk\Type\Catalog\Category;

use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\AbstractBulkAction;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class DeleteCategoriesBulkAction implements bulk deleting for categories grid.
 */
final class DeleteCategoriesBulkAction extends AbstractBulkAction
{
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'delete_categories';
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired([
                'categories_bulk_delete_route',
            ])
            ->setAllowedTypes('categories_bulk_delete_route', 'string');
    }
}
