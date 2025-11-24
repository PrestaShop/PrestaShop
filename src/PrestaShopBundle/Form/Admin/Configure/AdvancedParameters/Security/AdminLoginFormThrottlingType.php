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

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Security;

use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminLoginFormThrottlingType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'login_throttling_enabled',
                SwitchType::class,
                [
                    'required' => true,
                    'label' => $this->trans('Monitor and limit login attempts', 'Admin.Advparameters.Feature'),
                    'help' => $this->trans('Adds a short delay to repeated login attempts so bots and brute-force attacks cannot guess credentials at full speed.', 'Admin.Advparameters.Help'),
                ]
            )
            ->add(
                'login_throttling_max_attempts',
                IntegerType::class,
                [
                    'required' => true,
                    'attr' => [
                        'min' => 1,
                    ],
                    'label' => $this->trans('Allowed attempts before slowing down', 'Admin.Advparameters.Feature'),
                    'help' => $this->trans('How many incorrect logins are tolerated within the time window. Lower values stop password spraying faster.', 'Admin.Advparameters.Help'),
                ]
            )
            ->add(
                'login_throttling_interval',
                IntegerType::class,
                [
                    'required' => true,
                    'attr' => [
                        'min' => 1,
                    ],
                    'label' => $this->trans('Protection time window (minutes)', 'Admin.Advparameters.Feature'),
                    'help' => $this->trans('Duration used to count login attempts. A longer window covers extended attacks but might add delays for legitimate retries.', 'Admin.Advparameters.Help'),
                ]
            )
            ->add(
                'login_throttling_storage',
                TextType::class,
                [
                    'required' => false,
                    'label' => $this->trans('Custom storage service (advanced)', 'Admin.Advparameters.Feature'),
                    'help' => $this->trans('Optional Symfony service ID for the RateLimiter storage (for example, cache.app). Use it if your hosting needs to persist counters outside the default storage.', 'Admin.Advparameters.Help'),
                    'attr' => [
                        'placeholder' => 'cache.app',
                    ],
                ]
            )
        ;
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
        return 'security_admin_login_throttling_block';
    }
}
