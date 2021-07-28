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
use PrestaShop\PrestaShop\Adapter\Addons\AddonsDataProvider;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

class GeneralType extends TranslatorAwareType
{
    public const FIELD_FRONT_COOKIE_LIFETIME = 'front_cookie_lifetime';
    public const FIELD_BACK_COOKIE_LIFETIME = 'back_cookie_lifetime';
    public const FIELD_COOKIE_SAMESITE = 'cookie_samesite';

    /**
     * @var bool
     */
    private $isDebug;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param bool|null $isDebug
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        bool $isDebug = null
    ) {
        parent::__construct($translator, $locales);

        $this->isDebug = $isDebug === null ? (defined('_PS_MODE_DEV_') ? _PS_MODE_DEV_ : true) : $isDebug;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('check_modules_update', SwitchType::class, [
                'label' => $this->trans('Automatically check for module updates', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Choose a stability level for the modules downloaded from the Addons Marketplace. All zips pushed on Addons are in stable state unless stated otherwise.', 'Admin.Advparameters.Help'),
            ]);
        if ($this->isDebug) {
            $builder->add('check_modules_stability_channel', ChoiceType::class, [
                'label' => $this->trans('Addons API stability channel', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('New modules and updates are displayed on the modules page.', 'Admin.Advparameters.Help'),
                'required' => true,
                'choices' => $this->getStabilityChannelsValues(),
            ]);
        }
        $builder
            ->add('check_ip_address', SwitchType::class, [
                'label' => $this->trans('Check the cookie\'s IP address', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Check the IP address of the cookie in order to prevent your cookie from being stolen.', 'Admin.Advparameters.Help'),
            ])
            ->add(self::FIELD_FRONT_COOKIE_LIFETIME, TextType::class, [
                'label' => $this->trans('Lifetime of front office cookies', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Set the amount of hours during which the front office cookies are valid. After that amount of time, the customer will have to log in again.', 'Admin.Advparameters.Help'),
            ])
            ->add(self::FIELD_BACK_COOKIE_LIFETIME, TextType::class, [
                'label' => $this->trans('Lifetime of back office cookies', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('When you access your back office and decide to stay logged in, your cookies lifetime defines your browser session. Set here the number of hours during which you want them valid before logging in again.', 'Admin.Advparameters.Help'),
            ])
            ->add(self::FIELD_COOKIE_SAMESITE, ChoiceType::class, [
                'label' => $this->trans('Cookie SameSite', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Allows you to declare if your cookie should be restricted to a first-party or same-site context.', 'Admin.Advparameters.Help'),
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
        foreach (AddonsDataProvider::ADDONS_API_MODULE_CHANNELS as $key) {
            $values[$this->getStabilityChannelsValue($key)] = $key;
        }

        return $values;
    }

    private function getStabilityChannelsValue(string $value): string
    {
        switch ($value) {
            case AddonsDataProvider::ADDONS_API_MODULE_CHANNEL_ALPHA:
                return $this->trans('Alpha', 'Admin.Advparameters.Feature');
            case AddonsDataProvider::ADDONS_API_MODULE_CHANNEL_BETA:
                return $this->trans('Beta', 'Admin.Advparameters.Feature');
            case AddonsDataProvider::ADDONS_API_MODULE_CHANNEL_STABLE:
                return $this->trans('Stable', 'Admin.Advparameters.Feature');
        }

        return $value;
    }
}
