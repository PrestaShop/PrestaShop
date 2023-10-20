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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShopBundle\Form\Admin\Sell\Order\Delivery;

use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * This form class generates the "Options" form in Delivery slips page.
 */
class SlipOptionsType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'prefix',
                TranslatableType::class,
                [
                    'type' => TextType::class,
                    'label' => $this->trans('Delivery prefix', 'Admin.Orderscustomers.Feature'),
                    'label_help_box' => $this->trans('Prefix used for delivery slips.', 'Admin.Orderscustomers.Help'),
                ]
            )
            ->add(
                'number',
                NumberType::class,
                [
                    'label' => $this->trans('Delivery number', 'Admin.Orderscustomers.Feature'),
                    'label_help_box' => $this->trans(
                        'The next delivery slip will begin with this number and then increase with each additional slip.',
                        'Admin.Orderscustomers.Help'
                    ),
                ]
            )
            ->add(
                'enable_product_image',
                SwitchType::class,
                [
                    'label' => $this->trans('Enable product image', 'Admin.Orderscustomers.Feature'),
                    'label_help_box' => $this->trans(
                        'Add an image before the product name on delivery slips.',
                        'Admin.Orderscustomers.Help'
                    ),
                ]
            );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'order_delivery_slip_options';
    }
}
