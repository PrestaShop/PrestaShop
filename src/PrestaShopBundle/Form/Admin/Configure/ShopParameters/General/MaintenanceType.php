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

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\General;

use PrestaShopBundle\Form\Admin\Type\FormattedTextareaType;
use PrestaShopBundle\Form\Admin\Type\IpAddressType;
use PrestaShopBundle\Form\Admin\Type\MultistoreConfigurationType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslateType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use PrestaShopBundle\Form\Extension\MultistoreConfigurationTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class returning the content of the form in the maintenance page.
 * To be found in Configure > Shop parameters > General > Maintenance.
 */
class MaintenanceType extends TranslatorAwareType
{
    /**
     * @var string
     */
    private $currentIp;

    /**
     * MaintenanceType constructor.
     *
     * @param TranslatorInterface $translator
     * @param array<int, string> $locales
     * @param string $currentIp
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        string $currentIp
    ) {
        parent::__construct($translator, $locales);
        $this->currentIp = $currentIp;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'enable_shop',
                SwitchType::class,
                [
                    'required' => true,
                    'multistore_configuration_key' => 'PS_SHOP_ENABLE',
                    'label' => $this->trans('Enable store', 'Admin.Shopparameters.Feature'),
                    'help' => $this->trans(
                        'We recommend that you deactivate your store while performing maintenance. Note that it will not disable the webservice.',
                        'Admin.Shopparameters.Help'
                    ),
                ]
            )
            ->add(
                'maintenance_allow_admins',
                SwitchType::class,
                [
                    'required' => false,
                    'multistore_configuration_key' => 'PS_MAINTENANCE_ALLOW_ADMINS',
                    'label' => $this->trans('Enable store for logged-in employees', 'Admin.Shopparameters.Feature'),
                    'help' => $this->trans(
                        'When enabled, admins will access the store front office without storing their IP.',
                        'Admin.Shopparameters.Help'
                    ),
                ]
            )
            ->add(
                'maintenance_ip',
                IpAddressType::class,
                [
                    'required' => false,
                    'multistore_configuration_key' => 'PS_MAINTENANCE_IP',
                    'empty_data' => '',
                    'attr' => [
                        'class' => 'col-md-5',
                    ],
                    'label' => $this->trans('Maintenance IP', 'Admin.Shopparameters.Feature'),
                    'help' => $this->trans(
                        'Allow IP addresses to access the store, even in maintenance mode. Use a comma to separate them (e.g. 42.24.4.2,127.0.0.1,99.98.97.96).',
                        'Admin.Shopparameters.Help'
                    ),
                    'current_ip' => $this->currentIp,
                ]
            )
            ->add(
                'maintenance_text',
                TranslateType::class,
                [
                    'type' => FormattedTextareaType::class,
                    'options' => [
                        'required' => false,
                    ],
                    'locales' => $this->locales,
                    'hideTabs' => false,
                    'required' => true,
                    'multistore_configuration_key' => 'PS_MAINTENANCE_TEXT',
                    'label' => $this->trans('Custom maintenance text', 'Admin.Shopparameters.Feature'),
                    'help' => $this->trans(
                        'Display a customized message when the store is disabled.',
                        'Admin.Shopparameters.Help'
                    ),
                ]
            );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'Admin.Shopparameters.Feature',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'maintenance_general_block';
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
