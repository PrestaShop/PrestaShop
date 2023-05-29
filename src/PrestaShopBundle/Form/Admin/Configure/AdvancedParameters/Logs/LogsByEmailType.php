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

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Logs;

use PrestaShopBundle\Form\Admin\Type\LogSeverityChoiceType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This form class generates the "Logs by email" form in Logs page.
 */
final class LogsByEmailType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('logs_by_email', LogSeverityChoiceType::class, [
                'placeholder' => $this->trans(
                    'None',
                    'Admin.Global'
                ),
                'label' => $this->trans(
                    'Minimum severity level',
                    'Admin.Advparameters.Feature'
                ),
                'help' => $this->trans(
                    'Click on "None" to disable log alerts by email or enter the recipients of these emails in the following field.',
                    'Admin.Advparameters.Help'
                ),
            ])
            ->add('logs_email_receivers', TextType::class, [
                'label' => $this->trans(
                    'Send emails to',
                    'Admin.Advparameters.Feature'
                ),
                'help' => $this->trans(
                    'Log alerts will be sent to these emails. Please use a comma to separate them (e.g. pub@prestashop.com, anonymous@psgdpr.com).',
                    'Admin.Advparameters.Help'
                ),
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'translation_domain' => 'Admin.Advparameters.Feature',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'logs_by_email_block';
    }
}
