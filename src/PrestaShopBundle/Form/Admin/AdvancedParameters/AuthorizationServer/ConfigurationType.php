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

namespace PrestaShopBundle\Form\Admin\AdvancedParameters\AuthorizationServer;

use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ConfigurationType extends TranslatorAwareType
{
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        private readonly RouterInterface $router,
    ) {
        parent::__construct($translator, $locales);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $enableAdminAPIHelp = $this->trans(
            'Before enabling the Admin API, you must be sure to:',
            'Admin.Advparameters.Help'
        );
        $enableAdminAPIHelp .= '<br/> 1. ';
        $enableAdminAPIHelp .= $this->trans(
            'Check that the ps_apiressources module is installed and enabled.',
            'Admin.Advparameters.Help'
        );
        $enableAdminAPIHelp .= '<br/> 2. ';
        $enableAdminAPIHelp .= $this->trans(
            'Check that URL rewriting is available on this server.',
            'Admin.Advparameters.Help'
        );
        $enableAdminAPIHelp .= '<br/> 3. ';
        $enableAdminAPIHelp .= $this->trans(
            'Check that the six methods GET, POST, PUT, PATCH, DELETE and HEAD are supported by this server.',
            'Admin.Advparameters.Help'
        );
        $enableAdminAPIHelp .= '<br/>';
        $enableAdminAPIHelp .= $this->trans(
            'The multistore is still on beta, to test it out go the [1]%feature_flag_page%[/1] page',
            'Admin.Advparameters.Help',
            [
                '%feature_flag_page%' => $this->trans('New & Experimental features', 'Admin.Navigation.Menu'),
                '[1]' => '<a href="' . $this->router->generate('admin_feature_flags_index') . '" target="_blank">',
                '[/1]' => '</a>',
            ],
        );

        $builder
            ->add('enable_admin_api', SwitchType::class, [
                'label' => $this->trans('Admin API', 'Admin.Advparameters.Feature'),
                'help' => $enableAdminAPIHelp,
                'required' => true,
                'choices' => [
                    'Disabled' => false,
                    'Enabled' => true,
                ],
            ])
            ->add('enable_experimental_endpoints', SwitchType::class, [
                'label' => $this->trans('Enable experimental endpoints', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Experimental API endpoints are disabled by default in prod environment, this configuration allows to forcefully enable them (not recommended).', 'Admin.Advparameters.Feature'),
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'form_theme' => '@PrestaShop/Admin/TwigTemplateForm/prestashop_ui_kit.html.twig',
        ]);
    }
}
