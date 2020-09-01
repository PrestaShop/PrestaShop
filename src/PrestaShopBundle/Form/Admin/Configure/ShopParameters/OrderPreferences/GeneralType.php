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

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShopBundle\Form\Admin\Type\MoneyWithSuffixType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class generates "General" form
 * in "Configure > Shop Parameters > Order Settings" page.
 */
class GeneralType extends TranslatorAwareType
{
    /**
     * @var string
     */
    private $defaultCurrencyIsoCode;

    /**
     * CMS pages choices for Terms Of Service.
     *
     * @var array
     */
    private $tosCmsChoices;

    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        $defaultCurrencyIsoCode,
        array $tosCmsChoices
    ) {
        parent::__construct($translator, $locales);

        $this->defaultCurrencyIsoCode = $defaultCurrencyIsoCode;
        $this->tosCmsChoices = $tosCmsChoices;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Configuration $configuration */
        $configuration = $this->getConfiguration();
        $isMultishippingEnabled = $configuration->getBoolean('PS_ALLOW_MULTISHIPPING');
        $currencyIsoCode = $this->defaultCurrencyIsoCode;

        $builder
            ->add('enable_final_summary', SwitchType::class, [
                'required' => false,
                'label' => $this->trans('Enable final summary', 'Admin.Shopparameters.Feature'),
                'help' => $this->trans('Display an overview of the addresses, shipping method and cart just before the order button (required in some European countries).', 'Admin.Shopparameters.Help'),
            ])
            ->add('enable_guest_checkout', SwitchType::class, [
                'required' => false,
                'label' => $this->trans('Enable guest checkout', 'Admin.Shopparameters.Feature'),
                'help' => $this->trans('Allow guest visitors to place an order without registering.', 'Admin.Shopparameters.Help'),
            ])
            ->add('disable_reordering_option', SwitchType::class, [
                'required' => false,
                'label' => $this->trans('Disable reordering option', 'Admin.Shopparameters.Feature'),
                'help' => $this->trans('Disable the option to allow customers to reorder in one click from the order history page (required in some European countries).', 'Admin.Shopparameters.Help'),
            ])
            ->add('purchase_minimum_value', MoneyWithSuffixType::class, [
                'required' => false,
                'label' => $this->trans('Minimum purchase total required in order to validate the order', 'Admin.Shopparameters.Feature'),
                'help' => $this->trans('Set to 0 to disable this feature.', 'Admin.Shopparameters.Help'),
                'currency' => $currencyIsoCode,
                'suffix' => $this->trans('(tax excl.)', 'Admin.Global'),
            ])
            ->add('recalculate_shipping_cost', SwitchType::class, [
                'required' => false,
                'label' => $this->trans('Recalculate shipping costs after editing the order', 'Admin.Shopparameters.Feature'),
                'help' => $this->trans('Automatically updates the shipping costs when you edit an order.', 'Admin.Shopparameters.Help'),
            ]);

        if ($isMultishippingEnabled) {
            $builder->add('allow_multishipping', SwitchType::class, [
                'required' => false,
                'label' => $this->trans('Allow multishipping', 'Admin.Shopparameters.Feature'),
                'help' => $this->trans('Allow the customer to ship orders to multiple addresses. This option will convert the customer\'s cart into one or more orders.', 'Admin.Shopparameters.Help'),
            ]);
        }

        $builder
            ->add('allow_delayed_shipping', SwitchType::class, [
                'required' => false,
                'label' => $this->trans('Delayed shipping', 'Admin.Shopparameters.Feature'),
                'help' => $this->trans('It allows you to delay shipping if your customers request it.', 'Admin.Shopparameters.Help'),
            ])
            ->add('enable_tos', SwitchType::class, [
                'required' => false,
                'label' => $this->trans('Terms of service', 'Admin.Shopparameters.Feature'),
                'help' => $this->trans('Require customers to accept or decline terms of service before processing an order.', 'Admin.Shopparameters.Help'),
            ])
            ->add('tos_cms_id', ChoiceType::class, [
                'required' => false,
                'label' => $this->trans('Page for terms and conditions', 'Admin.Shopparameters.Feature'),
                'help' => $this->trans('Choose the page which contains your store\'s terms and conditions of use.', 'Admin.Shopparameters.Help'),
                'placeholder' => $this->trans('None', 'Admin.Global'),
                'choices' => $this->tosCmsChoices,
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
        return 'order_preferences_general_block';
    }
}
