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

namespace PrestaShopBundle\Form\Admin\Sell\Discount;

use PrestaShopBundle\Form\Admin\Type\CarrierChoiceType;
use PrestaShopBundle\Form\Admin\Type\CountryChoiceType;
use PrestaShopBundle\Form\Admin\Type\ToggleChildrenChoiceType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\When;

class DeliveryConditionsType extends TranslatorAwareType
{
    public const NONE = 'none';
    public const CARRIERS = 'carriers';
    public const COUNTRY = 'country';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(self::NONE, HiddenType::class, [
                'label' => $this->trans('None', 'Admin.Catalog.Feature'),
            ])
            ->add(self::CARRIERS, CarrierChoiceType::class, [
                'label' => $this->trans('Specific carriers', 'Admin.Catalog.Feature'),
                'multiple' => true,
                'attr' => [
                    'data-placeholder' => $this->trans('Select carriers', 'Admin.Catalog.Feature'),
                ],
                'constraints' => [
                    new When(
                        expression: sprintf(
                            'this.getParent().get("children_selector").getData() === "%s"',
                            self::CARRIERS
                        ),
                        constraints: new NotBlank(),
                    ),
                ],
            ])
            ->add(self::COUNTRY, CountryChoiceType::class, [
                'label' => $this->trans('Specific countries', 'Admin.Catalog.Feature'),
                'multiple' => true,
                'expanded' => false,
                'with_logo_attr' => true,
                'attr' => [
                    'data-placeholder' => $this->trans('Select countries', 'Admin.Catalog.Feature'),
                ],
                'constraints' => [
                    new When(
                        expression: sprintf(
                            'this.getParent().get("children_selector").getData() === "%s"',
                            self::COUNTRY
                        ),
                        constraints: new NotBlank(),
                    ),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'label' => $this->trans('On delivery', 'Admin.Catalog.Feature'),
        ]);
    }

    public function getParent()
    {
        return ToggleChildrenChoiceType::class;
    }
}
