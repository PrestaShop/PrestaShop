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

use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShop\PrestaShop\Core\Grid\Column\AbstractColumn;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Displays choices in the grid.
 */
final class ChoiceColumn extends AbstractColumn
{
    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return 'choice';
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver
            ->setRequired(
                [
                    'choice_provider',
                    'field',
                    'route',
                ]
            )
            ->setDefaults([
                'color_field' => '',
                'record_route_params' => [],
            ])
            ->setAllowedTypes('choice_provider', FormChoiceProviderInterface::class)
            ->setAllowedTypes('field', ['string', 'int', 'bool'])
            ->setAllowedTypes('color_field', 'string')
            ->setAllowedTypes('route', 'string')
            ->setAllowedTypes('record_route_params', 'array')
        ;
    }
}
