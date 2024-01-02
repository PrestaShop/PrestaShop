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

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\OrderPreferences;

use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShopBundle\Form\Admin\Type\MoneyWithSuffixType;
use PrestaShopBundle\Form\Admin\Type\MultistoreConfigurationType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use PrestaShopBundle\Form\Extension\MultistoreConfigurationTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class generates "Gift options" form
 * in "Configure > Shop Parameters > Order Settings" page.
 */
class GiftOptionsType extends TranslatorAwareType
{
    /**
     * @var string
     */
    private $defaultCurrencyIsoCode;

    /**
     * @var array
     */
    private $taxChoices;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param ConfigurationInterface $configuration
     * @param string $defaultCurrencyIsoCode
     * @param array $taxChoices
     * @param RouterInterface $router
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        ConfigurationInterface $configuration,
        $defaultCurrencyIsoCode,
        array $taxChoices,
        RouterInterface $router
    ) {
        parent::__construct($translator, $locales);

        $this->defaultCurrencyIsoCode = $defaultCurrencyIsoCode;
        $this->taxChoices = $taxChoices;
        $this->router = $router;
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $atcpShipWrap = (bool) $this->configuration->get('PS_ATCP_SHIPWRAP');
        $currencyIsoCode = $this->defaultCurrencyIsoCode;

        $builder
            ->add('enable_gift_wrapping', SwitchType::class, [
                'required' => false,
                'label' => $this->trans('Offer gift wrapping', 'Admin.Shopparameters.Feature'),
                'help' => $this->trans('Remember to regenerate email templates in [1]Design > Email theme[/1] after enabling or disabling this feature.',
                    'Admin.Shopparameters.Help',
                    [
                        '[1]' => '<a href="' . $this->router->generate('admin_mail_theme_index') . '" target="_blank">',
                        '[/1]' => '</a>',
                    ]
                ),
                'multistore_configuration_key' => 'PS_GIFT_WRAPPING',
            ])
            ->add('gift_wrapping_price', MoneyWithSuffixType::class, [
                'required' => false,
                'label' => $this->trans('Gift-wrapping price', 'Admin.Shopparameters.Feature'),
                'help' => $this->trans('Set a price for gift wrapping.', 'Admin.Shopparameters.Help'),
                'currency' => $currencyIsoCode,
                'suffix' => $this->trans('(tax excl.)', 'Admin.Global'),
                'multistore_configuration_key' => 'PS_GIFT_WRAPPING_PRICE',
            ]);

        if (!$atcpShipWrap) {
            $builder->add('gift_wrapping_tax_rules_group', ChoiceType::class, [
                'required' => false,
                'label' => $this->trans('Gift-wrapping tax', 'Admin.Shopparameters.Feature'),
                'help' => $this->trans('Set a tax for gift wrapping.', 'Admin.Shopparameters.Help'),
                'placeholder' => $this->trans('None', 'Admin.Global'),
                'choices' => $this->taxChoices,
                'multistore_configuration_key' => 'PS_GIFT_WRAPPING_TAX_RULES_GROUP',
            ]);
        }

        $builder->add('offer_recyclable_pack', SwitchType::class, [
            'required' => false,
            'label' => $this->trans('Offer recycled packaging', 'Admin.Shopparameters.Feature'),
            'help' => $this->trans('Suggest recycled packaging to customer.', 'Admin.Shopparameters.Help'),
            'multistore_configuration_key' => 'PS_RECYCLABLE_PACK',
        ]);
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
        return 'order_preferences_gift_options_block';
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
