<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Column\Type\Common;

use PrestaShop\PrestaShop\Core\Grid\Column\AbstractColumn;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class BadgeColumn displays column with badge.
 */
final class BadgeColumn extends AbstractColumn
{
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'badge';
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
                'badge_type' => 'success',
                'badge_type_field' => '',
                'empty_value' => '',
                'clickable' => true,
                'color_field' => '',
                'alignment' => 'right',
            ])
            ->setAllowedTypes('field', 'string')
            ->setAllowedTypes('empty_value', 'string')
            ->setAllowedTypes('clickable', 'bool')
            ->setAllowedValues('badge_type', ['success', 'info', 'danger', 'warning', 'light-info', ''])
            ->setAllowedTypes('badge_type_field', 'string');
    }
}
