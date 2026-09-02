<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\Customer;

use PrestaShop\PrestaShop\Core\Grid\Action\Row\AbstractRowAction;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class DeleteCustomerDiscountRowAction extends AbstractRowAction
{
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'delete_customer_discount';
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver
            ->setRequired([
                'id_cart_rule',
            ])
            ->setDefaults([
                'method' => 'POST',
                'confirm_message' => '',
                'modal_options' => null,
            ])
            ->setAllowedTypes('id_cart_rule', 'string')
            ->setAllowedTypes('method', 'string')
            ->setAllowedTypes('confirm_message', 'string')
        ;
    }
}
