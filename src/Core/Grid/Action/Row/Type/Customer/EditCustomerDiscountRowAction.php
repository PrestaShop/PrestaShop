<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\Customer;

use PrestaShop\PrestaShop\Core\Grid\Action\Row\AbstractRowAction;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class EditCustomerDiscountRowAction extends AbstractRowAction
{
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'edit_customer_discount';
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
            ->setAllowedTypes('id_cart_rule', 'string')
        ;
    }
}
