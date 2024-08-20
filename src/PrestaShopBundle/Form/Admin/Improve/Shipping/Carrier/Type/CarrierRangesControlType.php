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

namespace PrestaShopBundle\Form\Admin\Improve\Shipping\Carrier\Type;

use PrestaShopBundle\Form\Admin\Type\MultipleZoneChoiceType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class CarrierRangesControlType extends TranslatorAwareType
{
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        private readonly RouterInterface $router,
    ) {
        parent::__construct($translator, $locales);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('zones', MultipleZoneChoiceType::class, [
                'label' => false,
                'required' => false,
                'multiple' => true,
                'external_link' => [
                    'text' => $this->trans('[1]Manage locations[/1]', 'Admin.Shipping.Feature'),
                    'position' => 'below',
                    'href' => $this->router->generate('admin_zones_index'),
                    'attr' => [
                        'target' => '_blank',
                    ],
                ],
                'attr' => [
                    'data-placeholder' => $this->trans('Zones', 'Admin.Shipping.Feature'),
                    'class' => 'select2 js-multiple-zone-choice',
                ],
            ])
            ->add('ranges', CarrierRangesType::class, [
                'label' => false,
                'row_attr' => [
                    'class' => 'carrier-ranges-edit-row',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'label' => $this->trans('Zones', 'Admin.Shipping.Feature'),
            'label_help_box' => $this->trans('Zones that the carrier can handle', 'Admin.Shipping.Help'),
            'attr' => [
                'class' => 'carrier-ranges-control',
            ],
        ]);
    }
}
