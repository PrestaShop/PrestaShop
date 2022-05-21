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

namespace PrestaShopBundle\Form\Admin\Sell\Product\Options;

use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

class VisibilityType extends TranslatorAwareType
{
    /**
     * @var FormChoiceProviderInterface
     */
    private $productVisibilityChoiceProvider;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param FormChoiceProviderInterface $productVisibilityChoiceProvider
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        FormChoiceProviderInterface $productVisibilityChoiceProvider
    ) {
        parent::__construct($translator, $locales);
        $this->productVisibilityChoiceProvider = $productVisibilityChoiceProvider;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('visibility', ChoiceType::class, [
                'label' => false,
                'choices' => $this->productVisibilityChoiceProvider->getChoices(),
                'expanded' => true,
                'required' => false,
                // placeholder false is important to avoid empty option in radio select despite required being false
                'placeholder' => false,
                'column_breaker' => true,
                'modify_all_shops' => true,
            ])
            ->add('available_for_order', SwitchType::class, [
                'label' => $this->trans('Available for order', 'Admin.Catalog.Feature'),
                'required' => false,
                'modify_all_shops' => true,
            ])
            ->add('show_price', SwitchType::class, [
                'label' => $this->trans('Show price', 'Admin.Catalog.Feature'),
                'required' => false,
                'modify_all_shops' => true,
                'row_attr' => [
                    'class' => 'show-price-switch-container',
                ],
            ])
            ->add('online_only', SwitchType::class, [
                'label' => $this->trans('Web only (not sold in your retail store)', 'Admin.Catalog.Feature'),
                'required' => false,
                'modify_all_shops' => true,
            ])
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'label' => $this->trans('Visibility', 'Admin.Catalog.Feature'),
            'label_tag_name' => 'h3',
            'label_subtitle' => $this->trans('Where do you want your product to appear?', 'Admin.Catalog.Feature'),
            'required' => false,
            'columns_number' => 4,
        ]);
    }
}
