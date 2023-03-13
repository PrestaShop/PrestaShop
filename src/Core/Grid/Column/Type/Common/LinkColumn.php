<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\Grid\Column\Type\Common;

use PrestaShop\PrestaShop\Core\Grid\Column\AbstractColumn;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class LinkColumn is used to define a column in which there is a link targeting an action route (view, edit, add...).
 *
 * Example:
 * new LinkColumn('name'))
 *  ->setName('Name')
 *   ->setOptions([
 *       'field' => 'name',
 *       'route' => 'admin_edit',
 *       'route_param_name' => 'myId',
 *       'route_param_field' => 'id',
 *   ])
 */
final class LinkColumn extends AbstractColumn
{
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'link';
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver
            ->setDefaults([
                'icon' => null,
                'route_fragment' => null,
                'button_template' => false,
                'color_template' => 'primary',
                'color_template_field' => null,
            ])
            ->setRequired([
                'field',
                'route',
                'route_param_name',
                'route_param_field',
            ])
            ->setDefined([
                'icon',
                'target',
            ])
            ->setAllowedTypes('field', ['string', 'null'])
            ->setAllowedTypes('icon', ['string', 'null'])
            ->setAllowedTypes('target', ['string', 'null'])
            ->setAllowedTypes('color_template_field', ['string', 'null'])
            ->setAllowedTypes('sortable', 'bool')
            ->setAllowedTypes('route', 'string')
            ->setAllowedTypes('route_fragment', ['string', 'null'])
            ->setAllowedTypes('route_param_name', 'string')
            ->setAllowedTypes('route_param_field', 'string')
            ->setAllowedTypes('clickable', 'bool')
            ->setAllowedValues('color_template', [
                'primary',
                'secondary',
                'success',
                'danger',
                'warning',
                'info',
            ])
            ->setAllowedValues('button_template', [
                false,
                'outline',
                'normal',
            ])
        ;
    }
}
