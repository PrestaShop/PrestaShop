<?php
/**
 * 2007-2018 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
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
            ->add('enable_gift_wrapping', SwitchType::class)
            ->add('gift_wrapping_price', MoneyWithSuffixType::class, [
                'required' => false,
                'currency' => $currencyIsoCode,
                'suffix' => $this->trans('(tax excl.)', 'Admin.Global'),
            ]);

        if (!$atcpShipWrap) {
            $builder->add('gift_wrapping_tax_rules_group', ChoiceType::class, [
                'required' => false,
                'placeholder' => $this->trans('None', 'Admin.Global'),
                'choices' => $this->taxChoices,
            ]);
        }

        $builder->add('offer_recyclable_pack', SwitchType::class);
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
