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

use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Multistore\MultistoreContextCheckerInterface;
use PrestaShopBundle\Form\Admin\Type\ConfigurationType;
use PrestaShopBundle\Form\Admin\Type\FormattedTextareaType;
use PrestaShopBundle\Form\Admin\Type\IpAddressType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslateType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class returning the content of the form in the maintenance page.
 * To be found in Configure > Shop parameters > General > Maintenance.
 */
class MaintenanceType extends TranslatorAwareType
{
    /**
     * @var ShopConfigurationInterface
     */
    private $shopConfiguration;

    /**
     * @var bool
     */
    private $isAllShopContext;

    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        ShopConfigurationInterface $shopConfiguration,
        MultistoreContextCheckerInterface $multistoreContext
    ) {
        parent::__construct($translator, $locales);
        $this->shopConfiguration = $shopConfiguration;
        $this->isAllShopContext = $multistoreContext->isAllShopContext();
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
                    'attr' => [
                        'multistore_configuration_key' => 'PS_SHOP_ENABLE',
                        'disabled' => !$this->isAllShopContext && !$this->shopConfiguration->isOverridenByCurrentContext('PS_SHOP_ENABLE'),
                    ],
                ]
            )
            ->add(
                'maintenance_ip',
                IpAddressType::class,
                [
                    'required' => false,
                    'empty_data' => '',
                    'attr' => [
                        'class' => 'col-md-5',
                        'multistore_configuration_key' => 'PS_MAINTENANCE_IP',
                        'disabled' => !$this->isAllShopContext && !$this->shopConfiguration->isOverridenByCurrentContext('PS_MAINTENANCE_IP'),
                    ],
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
                    'disabled' => true,
                    'attr' => [
                        'multistore_configuration_key' => 'PS_MAINTENANCE_TEXT',
                        'disabled' => !$this->isAllShopContext && !$this->shopConfiguration->isOverridenByCurrentContext('PS_MAINTENANCE_TEXT'),
                    ],
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

    public function getParent(): string
    {
        return ConfigurationType::class;
    }
}
