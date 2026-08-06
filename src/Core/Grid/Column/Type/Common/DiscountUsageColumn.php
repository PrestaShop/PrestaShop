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
 * Displays discount usage as "quantityUsed / totalQuantity" with an infinity symbol for unlimited discounts.
 */
final class DiscountUsageColumn extends AbstractColumn
{
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'discount_usage';
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver
            ->setRequired([
                'quantity_used_field',
                'total_quantity_field',
            ])
            ->setDefaults([
                'clickable' => true,
            ])
            ->setAllowedTypes('quantity_used_field', 'string')
            ->setAllowedTypes('total_quantity_field', 'string')
            ->setAllowedTypes('clickable', 'bool')
        ;
    }
}
