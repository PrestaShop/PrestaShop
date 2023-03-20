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

namespace PrestaShopBundle\Form\Admin\Sell\CartRule;

use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Sell\Customer\SearchedCustomerType;
use PrestaShopBundle\Form\Admin\Type\DateRangeType;
use PrestaShopBundle\Form\Admin\Type\EntitySearchInputType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ConditionsType extends TranslatorAwareType
{
    /**
     * @var FormChoiceProviderInterface
     */
    private $currencyByIdChoiceProvider;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        FormChoiceProviderInterface $currencyByIdChoiceProvider,
        UrlGeneratorInterface $urlGenerator
    ) {
        parent::__construct($translator, $locales);
        $this->currencyByIdChoiceProvider = $currencyByIdChoiceProvider;
        $this->urlGenerator = $urlGenerator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('customer', EntitySearchInputType::class, [
                'label' => $this->trans('Limit to single customer', 'Admin.Global'),
                'layout' => EntitySearchInputType::LIST_LAYOUT,
                'entry_type' => SearchedCustomerType::class,
                'entry_options' => [
                    'block_prefix' => 'searched_customer',
                ],
                'allow_delete' => false,
                'limit' => 1,
                'disabling_switch' => true,
                'disabling_switch_event' => 'switchSpecificPriceCustomer',
                'switch_state_on_disable' => 'on',
                'disabled_value' => function ($data) {
                    return empty($data[0]['id_customer']);
                },
                'remote_url' => $this->urlGenerator->generate('admin_customers_search', ['customer_search' => '__QUERY__']),
                'placeholder' => $this->trans('Search customer', 'Admin.Actions'),
                'suggestion_field' => 'fullname_and_email',
                'required' => false,
            ])
            ->add('valid_date_range', DateRangeType::class, [
                'date_format' => 'YYYY-MM-DD HH:mm:ss',
            ])
            ->add('minimum_amount', NumberType::class)
            ->add('currency', ChoiceType::class, [
                'choices' => $this->currencyByIdChoiceProvider->getChoices(),
            ])
            ->add('tax_included', SwitchType::class)
            ->add('shipping_included', SwitchType::class)
            ->add('total_available', NumberType::class)
            ->add('available_per_user', NumberType::class)
            ->add('restrictions', RestrictionsType::class)
        ;
    }
}
