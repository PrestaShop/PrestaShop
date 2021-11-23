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

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\FeatureFlag;

use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagsModifier;
use PrestaShopBundle\Entity\FeatureFlag;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Represents the form used to manage feature flags state.
 * There is one SwitchType per existing feature flag.
 */
class FeatureFlagsType extends TranslatorAwareType
{
    /**
     * @var FeatureFlagsModifier
     */
    private $featureFlagsModifier;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param FeatureFlagsModifier $featureFlagsModifier
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        FeatureFlagsModifier $featureFlagsModifier
    ) {
        parent::__construct($translator, $locales);

        $this->featureFlagsModifier = $featureFlagsModifier;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var array<int, FeatureFlag> $allFeatureFlags */
        $allFeatureFlags = $this->featureFlagsModifier->getAllFeatureFlags();

        $enabledWording = $this->trans('Enabled', 'Admin.Global');
        $disabledWording = $this->trans('Disabled', 'Admin.Global');

        /** @var FeatureFlag $featureFlag */
        foreach ($allFeatureFlags as $featureFlag) {
            $builder->add($featureFlag->getName(),
                SwitchType::class, [
                    'label' => $this->trans($featureFlag->getLabelWording(), $featureFlag->getLabelDomain()),
                    'help' => $this->trans($featureFlag->getDescriptionWording(), $featureFlag->getDescriptionDomain()),
                    'choices' => [
                        $disabledWording => false,
                        $enabledWording => true,
                    ],
                ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'method' => 'POST',
        ]);
    }
}
