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

    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        $defaultCurrencyIsoCode,
        array $taxChoices
    ) {
        parent::__construct($translator, $locales);

        $this->defaultCurrencyIsoCode = $defaultCurrencyIsoCode;
        $this->taxChoices = $taxChoices;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Configuration $configuration */
        $configuration = $this->getConfiguration();
        $atcpShipWrap = $configuration->getBoolean('PS_ATCP_SHIPWRAP');
        $currencyIsoCode = $this->defaultCurrencyIsoCode;

        $builder
            ->add('enable_gift_wrapping', SwitchType::class, [
                'required' => false,
                'label' => $this->trans('Offer gift wrapping', 'Admin.Shopparameters.Feature'),
                'help' => $this->trans('Suggest gift-wrapping to customers.', 'Admin.Shopparameters.Help'),
            ])
            ->add('gift_wrapping_price', MoneyWithSuffixType::class, [
                'required' => false,
                'label' => $this->trans('Gift-wrapping price', 'Admin.Shopparameters.Feature'),
                'help' => $this->trans('Set a price for gift wrapping.', 'Admin.Shopparameters.Help'),
                'currency' => $currencyIsoCode,
                'suffix' => $this->trans('(tax excl.)', 'Admin.Global'),
            ]);

        if (!$atcpShipWrap) {
            $builder->add('gift_wrapping_tax_rules_group', ChoiceType::class, [
                'required' => false,
                'label' => $this->trans('Gift-wrapping tax', 'Admin.Shopparameters.Feature'),
                'help' => $this->trans('Set a tax for gift wrapping.', 'Admin.Shopparameters.Help'),
                'placeholder' => $this->trans('None', 'Admin.Global'),
                'choices' => $this->taxChoices,
            ]);
        }

        $builder->add('offer_recyclable_pack', SwitchType::class, [
            'required' => false,
            'label' => $this->trans('Offer recycled packaging', 'Admin.Shopparameters.Feature'),
            'help' => $this->trans('Suggest recycled packaging to customer.', 'Admin.Shopparameters.Help'),
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
}
