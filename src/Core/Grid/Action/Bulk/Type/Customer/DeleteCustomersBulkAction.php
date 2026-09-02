<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Action\Bulk\Type\Customer;

use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\AbstractBulkAction;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Allows to configure "Delete" customers bulk action.
 */
final class DeleteCustomersBulkAction extends AbstractBulkAction
{
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'delete_customers';
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired([
                'customers_bulk_delete_route',
            ])
            ->setAllowedTypes('customers_bulk_delete_route', 'string')
        ;
    }
}
