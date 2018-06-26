<?php
/**
 * 2007-2018 PrestaShop
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
use PrestaShop\PrestaShop\Adapter\Currency\CurrencyDataProvider;
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
     * @var CurrencyDataProvider
     */
    private $currencyDataProvider;

    /**
     * CMS pages choices for Terms Of Service
     *
     * @var array
     */
    private $tosCmsChoices;

    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        CurrencyDataProvider $currencyDataProvider,
        array $tosCmsChoices
    ) {
        parent::__construct($translator, $locales);

        $this->currencyDataProvider = $currencyDataProvider;
        $this->tosCmsChoices = $tosCmsChoices;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Configuration $configuration */
        $configuration = $this->getConfiguration();
        $isMultishippingEnabled = $configuration->getBoolean('PS_ALLOW_MULTISHIPPING');
        $defaultCurrencyId = $configuration->getInt('PS_CURRENCY_DEFAULT');
        $defaultCurrency = $this->currencyDataProvider->getCurrencyById($defaultCurrencyId);

        $builder
            ->add('enable_final_summary', SwitchType::class)
            ->add('enable_guest_checkout', SwitchType::class)
            ->add('disable_reordering_option', SwitchType::class)
            ->add('purchase_minimum_value', MoneyWithSuffixType::class, [
                'currency' => $defaultCurrency->iso_code,
                'suffix' => $this->trans('(tax excl.)', 'Admin.Global'),
            ])
            ->add('recalculate_shipping_cost', SwitchType::class);

        if ($isMultishippingEnabled) {
            $builder->add('allow_multishipping', SwitchType::class);
        }

        $builder
            ->add('allow_delayed_shipping', SwitchType::class)
            ->add('enable_tos', SwitchType::class)
            ->add('tos_cms_id', ChoiceType::class, [
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
