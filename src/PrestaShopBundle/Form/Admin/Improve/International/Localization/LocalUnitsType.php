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

namespace PrestaShopBundle\Form\Admin\Improve\International\Localization;

use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class LocalUnitsType is responsible for building 'Improve > International > Localization' page
 * 'Local units' form.
 */
class LocalUnitsType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('weight_unit', TextType::class, [
                'label' => $this->trans(
                    'Weight unit',
                    'Admin.International.Feature'
                ),
                'help' => $this->trans(
                    'The default weight unit for your shop (e.g. "kg" for kilograms, "lbs" for pound-mass, etc.).',
                    'Admin.International.Help'
                ),
            ])
            ->add('distance_unit', TextType::class, [
                'label' => $this->trans(
                    'Distance unit',
                    'Admin.International.Feature'
                ),
                'help' => $this->trans(
                    'The default distance unit for your shop (e.g. "km" for kilometer, "mi" for mile, etc.).',
                    'Admin.International.Help'
                ),
            ])
            ->add('volume_unit', TextType::class, [
                'label' => $this->trans(
                    'Volume unit',
                    'Admin.International.Feature'
                ),
                'help' => $this->trans(
                    'The default volume unit for your shop (e.g. "L" for liter, "gal" for gallon, etc.).',
                    'Admin.International.Help'
                ),
            ])
            ->add('dimension_unit', TextType::class, [
                'label' => $this->trans(
                    'Dimension unit',
                    'Admin.International.Feature'
                ),
                'help' => $this->trans(
                    'The default dimension unit for your shop (e.g. "cm" for centimeter, "in" for inch, etc.).',
                    'Admin.International.Help'
                ),
            ]);
    }
}
