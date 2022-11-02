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

namespace PrestaShopBundle\Form\Admin\Improve\Shipping\Preferences;

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\Currency\CurrencyDataProvider;
use PrestaShopBundle\Form\Admin\Type\MoneyWithSuffixType;
use PrestaShopBundle\Form\Admin\Type\MultistoreConfigurationType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Type;

/**
 * Class generates "Package" form
 * in "Improve > Shipping > Preferences" page.
 */
class PackageType extends TranslatorAwareType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Configuration $configuration */
        $configuration = $this->getConfiguration();
        $weightUnit = $configuration->get('PS_WEIGHT_UNIT');
        $dimensionUnit = $configuration->get('PS_DIMENSION_UNIT');

        $builder
            ->add('package_weight', NumberType::class, [
                'unit' => $weightUnit,
                'required' => false,
                'empty_data' => '0',
                'label' => $this->trans(
                    'Package weight',
                    'Admin.Shipping.Feature'
                ),
                'constraints' => [
                    new GreaterThanOrEqual(['value' => 0]),
                    new Type(['type' => 'numeric']),
                ],
                'multistore_configuration_key' => 'PS_PACKAGE_WEIGHT',
            ])
            ->add('package_width', NumberType::class, [
                'unit' => $dimensionUnit,
                'required' => false,
                'empty_data' => '0',
                'label' => $this->trans(
                    'Package width',
                    'Admin.Shipping.Feature'
                ),
                'constraints' => [
                    new GreaterThanOrEqual(['value' => 0]),
                    new Type(['type' => 'numeric']),
                ],
                'multistore_configuration_key' => 'PS_PACKAGE_WIDTH',
            ])
            ->add('package_height', NumberType::class, [
                'unit' => $dimensionUnit,
                'required' => false,
                'empty_data' => '0',
                'label' => $this->trans(
                    'Package height',
                    'Admin.Shipping.Feature'
                ),
                'constraints' => [
                    new GreaterThanOrEqual(['value' => 0]),
                    new Type(['type' => 'numeric']),
                ],
                'multistore_configuration_key' => 'PS_PACKAGE_HEIGHT',
            ])
            ->add('package_depth', NumberType::class, [
                'unit' => $dimensionUnit,
                'required' => false,
                'empty_data' => '0',
                'label' => $this->trans(
                    'Package depth',
                    'Admin.Shipping.Feature'
                ),
                'constraints' => [
                    new GreaterThanOrEqual(['value' => 0]),
                    new Type(['type' => 'numeric']),
                ],
                'multistore_configuration_key' => 'PS_PACKAGE_DEPTH',
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'Admin.Shipping.Feature',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'shipping_preferences_package_block';
    }

    /**
     * {@inheritdoc}
     *
     * @see MultistoreConfigurationTypeExtension
     */
    public function getParent(): string
    {
        return MultistoreConfigurationType::class;
    }
}
