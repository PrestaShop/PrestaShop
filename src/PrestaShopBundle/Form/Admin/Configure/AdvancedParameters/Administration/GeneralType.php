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

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Administration;

use Cookie;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tools;

class GeneralType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('check_modules_update', SwitchType::class, [
                'required' => true,
            ])
            ->add('check_modules_stability_channel', ChoiceType::class, [
                'required' => true,
                'choices' => $this->getStabilityChannelsValues(),
            ])
            ->add('check_ip_address', SwitchType::class, [
                'required' => true,
            ])
            ->add('front_cookie_lifetime', TextType::class, [
                'required' => true,
            ])
            ->add('back_cookie_lifetime', TextType::class, [
                'required' => true,
            ])
            ->add('cookie_samesite', ChoiceType::class, [
                'required' => true,
                'choices' => Cookie::SAMESITE_AVAILABLE_VALUES,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'Admin.Advparameters.Feature',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'administration_general_block';
    }

    /**
     * @return array<string, string>
     */
    private function getStabilityChannelsValues(): array
    {
        $values = [];
        foreach (Tools::ADDONS_API_MODULE_CHANNELS as $key) {
            $values[$key] = $this->getStabilityChannelsValue($key);
        }

        return $values;
    }

    private function getStabilityChannelsValue(string $value): string
    {
        switch ($value) {
            case Tools::ADDONS_API_MODULE_CHANNEL_ALPHA:
                return $this->trans('Alpha', 'Admin.Advparameters.Feature');
            case Tools::ADDONS_API_MODULE_CHANNEL_BETA:
                return $this->trans('Beta', 'Admin.Advparameters.Feature');
            case Tools::ADDONS_API_MODULE_CHANNEL_STABLE:
                return $this->trans('Stable', 'Admin.Advparameters.Feature');
        }

        return $value;
    }
}
