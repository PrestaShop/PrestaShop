<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
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
        ;

        $resolver->setNormalizer('route_param_name', static function (Options $options, $value) {
            if (!empty($value)) {
                return $value;
            }

            // Fallback on route_param_id if it's specified
            if (!empty($options['route_param_id'])) {
                return $options['route_param_id'];
            }

            throw new MissingOptionsException(
                sprintf('Option "%s" is missing for "%s" column options.', 'route_param_name', self::class)
            );
        });
    }
}
