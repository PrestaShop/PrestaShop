<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Column\Type\Common;

use PrestaShop\PrestaShop\Core\Grid\Column\AbstractColumn;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This Column is used to display booleans.
 * - it will display an icon instead of the value
 * - if user clicks on it, this triggers a toggle of the boolean value.
 */
final class ToggleColumn extends AbstractColumn
{
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'toggle';
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'sortable' => true,
                // @deprecated, use route_param_name option instead
                'route_param_id' => '',
                'route_param_name' => '',
                'extra_route_params' => [],
            ])
            ->setRequired([
                'field',
                'primary_field',
                'route',
            ])
            ->setAllowedTypes('field', 'string')
            ->setAllowedTypes('primary_field', 'string')
            ->setAllowedTypes('route', 'string')
            ->setAllowedTypes('route_param_name', 'string')
            ->setAllowedTypes('sortable', 'bool')
            ->setAllowedTypes('route_param_id', 'string')
            ->setAllowedTypes('extra_route_params', 'array')
        ;

        $resolver->setNormalizer('route_param_name', static function (Options $options, $value) {
            if (!empty($value)) {
                return $value;
            }

            // Fallback on route_param_id if it's specified
            if (!empty($options['route_param_id'])) {
                return $options['route_param_id'];
            }

            throw new MissingOptionsException(sprintf('Option "%s" is missing for "%s" column options.', 'route_param_name', self::class));
        });
    }
}
