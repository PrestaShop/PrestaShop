<?php
/**
 * 2007-2018 PrestaShop.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Grid\Action\Row\Type;

use PrestaShop\PrestaShop\Core\Grid\Action\Row\AbstractRowAction;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class SubmitRowAction defines row action as form submit.
 */
final class SubmitRowAction extends AbstractRowAction
{
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'submit';
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired([
                'route',
                'route_param_name',
                'route_param_field',
            ])
            ->setDefaults([
                'method' => 'POST',
                'confirm_message' => '',
            ])
            ->setAllowedTypes('route', 'string')
            ->setAllowedTypes('route_param_name', 'string')
            ->setAllowedTypes('route_param_field', 'string')
            ->setAllowedTypes('method', 'string')
            ->setAllowedTypes('confirm_message', 'string')
        ;
    }
}
