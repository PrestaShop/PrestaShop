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

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Adds the "alert_message" option to all Form Types.
 *
 * You can use it together with the UI kit form theme to add alter message to your field
 */
class AlertExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'alert_message' => null,
                'alert_type' => 'info',
                'alert_position' => 'append',
                'alert_title' => null,
            ])
            ->setAllowedTypes('alert_message', ['null', 'string', 'array'])
            ->setAllowedTypes('alert_title', ['string', 'null'])
            ->setAllowedTypes('alert_type', ['string'])
            ->setAllowedTypes('alert_position', ['string'])
            ->setAllowedValues('alert_position', ['append', 'prepend'])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (!empty($options['alert_message'])) {
            $view->vars['alert_message'] = is_string($options['alert_message']) ? [$options['alert_message']] : $options['alert_message'];
            $view->vars['alert_type'] = $options['alert_type'];
            $view->vars['alert_position'] = $options['alert_position'];

            if (is_string($options['alert_title'])) {
                $view->vars['alert_title'] = $options['alert_title'];
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return FormType::class;
    }
}
