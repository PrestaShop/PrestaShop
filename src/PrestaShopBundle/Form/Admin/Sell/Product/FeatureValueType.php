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

namespace PrestaShopBundle\Form\Admin\Sell\Product;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DefaultLanguage;
use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;

class FeatureValueType extends TranslatorAwareType
{
    /**
     * @var FormChoiceProviderInterface
     */
    private $featuresChoiceProvider;

    /**
     * @var ConfigurableFormChoiceProviderInterface
     */
    private $featureValuesChoiceProvider;

    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        FormChoiceProviderInterface $featuresChoiceProvider,
        ConfigurableFormChoiceProviderInterface $featureValuesChoiceProvider
    ) {
        parent::__construct($translator, $locales);
        $this->featuresChoiceProvider = $featuresChoiceProvider;
        $this->featureValuesChoiceProvider = $featureValuesChoiceProvider;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $features = $this->featuresChoiceProvider->getChoices();
        $firstFeatureId = reset($features);
        $featureValues = $this->featureValuesChoiceProvider->getChoices(['feature_id' => $firstFeatureId]);

        $builder
            ->add('feature_id', ChoiceType::class, [
                'choices' => $features,
                'label' => $this->trans('Feature', 'Admin.Catalog.Feature'),
            ])
            ->add('feature_value_id', ChoiceType::class, [
                'choices' => $featureValues,
                'label' => $this->trans('Pre-defined value', 'Admin.Catalog.Feature'),
            ])
            ->add('custom_value', TranslatableType::class, [
                'label' => $this->trans('OR Customized value', 'Admin.Catalog.Feature'),
                'type' => TextType::class,
                'constraints' => [
                    new DefaultLanguage(),
                ],
            ])
        ;
    }
}
