<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\Customer;

use PrestaShop\PrestaShop\Core\Grid\Action\Row\AbstractRowAction;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class DeleteCustomerRowAction extends AbstractRowAction
{
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'delete_customer';
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver
            ->setRequired([
                'customer_id_field',
                'customer_delete_route',
            ])
            ->setAllowedTypes('customer_id_field', 'string')
            ->setAllowedTypes('customer_delete_route', 'string')
        ;
    }
}
