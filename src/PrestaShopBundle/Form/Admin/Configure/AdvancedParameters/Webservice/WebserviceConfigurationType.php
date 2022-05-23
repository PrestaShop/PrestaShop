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

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Webservice;

use PrestaShopBundle\Form\Admin\Type\MultistoreConfigurationType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This form class generates the "Webservice configuration" form in Webservice page.
 */
class WebserviceConfigurationType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $enableWebservicesHelp = $this->trans(
            'Before activating the webservice, you must be sure to: ',
            'Admin.Advparameters.Help'
        );
        $enableWebservicesHelp .= '<br/> 1. ';
        $enableWebservicesHelp .= $this->trans(
            'Check that URL rewriting is available on this server.',
            'Admin.Advparameters.Help'
        );
        $enableWebservicesHelp .= '<br/> 2. ';
        $enableWebservicesHelp .= $this->trans(
            'Check that the six methods GET, POST, PUT, PATCH, DELETE and HEAD are supported by this server.',
            'Admin.Advparameters.Help'
        );

        $builder
            ->add('enable_webservice', SwitchType::class, [
                'label' => $this->trans(
                    'Enable PrestaShop\'s webservice',
                    'Admin.Advparameters.Feature'
                ),
                'help' => $enableWebservicesHelp,
                'multistore_configuration_key' => 'PS_WEBSERVICE',
                'required' => true,
            ])
            ->add('enable_cgi', SwitchType::class, [
                'label' => $this->trans(
                    'Enable CGI mode for PHP',
                    'Admin.Advparameters.Feature'
                ),
                'help' => $this->trans(
                    'Before choosing "Yes", check that PHP is not configured as an Apache module on your server.',
                    'Admin.Advparameters.Help'
                ),
                'multistore_configuration_key' => 'PS_WEBSERVICE_CGI_HOST',
                'required' => true,
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
        return 'webservice_configuration';
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
