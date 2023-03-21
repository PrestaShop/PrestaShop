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

use PrestaShopBundle\Form\Admin\Sell\Customer\SearchedCustomerType;
use PrestaShopBundle\Form\Admin\Type\DateRangeType;
use PrestaShopBundle\Form\Admin\Type\EntitySearchInputType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ConditionsType extends TranslatorAwareType
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        UrlGeneratorInterface $urlGenerator
    ) {
        parent::__construct($translator, $locales);
        $this->urlGenerator = $urlGenerator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('customer', EntitySearchInputType::class, [
                'label' => $this->trans('Limit to single customer', 'Admin.Global'),
                'help' => $this->trans(
                    'Optional: The cart rule will be available to everyone if you leave this field blank.',
                    'Admin.Catalog.Help'
                ),
                'layout' => EntitySearchInputType::LIST_LAYOUT,
                'entry_type' => SearchedCustomerType::class,
                'entry_options' => [
                    'block_prefix' => 'cart_rule_searched_customer',
                ],
                'allow_delete' => false,
                'limit' => 1,
                'disabling_switch' => true,
                'disabling_switch_event' => 'switchSpecificPriceCustomer',
                'disabled_value' => function ($data) {
                    return empty($data[0]['id_customer']);
                },
                'remote_url' => $this->urlGenerator->generate('admin_customers_search', ['customer_search' => '__QUERY__']),
                'placeholder' => $this->trans('Search customer', 'Admin.Actions'),
                'suggestion_field' => 'fullname_and_email',
                'required' => false,
            ])
            ->add('valid_date_range', DateRangeType::class, [
                'label' => $this->trans('Valid', 'Admin.Catalog.Feature'),
                //@todo: help label does not appear (probably missing in form theme)
                'help' => $this->trans('The default period is one month.', 'Admin.Catalog.Help'),
                'required' => false,
                'date_format' => 'YYYY-MM-DD HH:mm:ss',
            ])
            ->add('minimum_amount', MinimumAmountType::class, [
                'label' => $this->trans('Minimum amount', 'Admin.Catalog.Feature'),
                'required' => false,
                'disabling_switch' => true,
                'disabled_value' => static function (?array $data): bool {
                    return empty($data['amount']);
                },
            ])
            ->add('total_available', NumberType::class, [
                'label' => $this->trans('Total available', 'Admin.Catalog.Feature'),
                'help' => $this->trans(
                    'The cart rule will be applied to the first "X" customers only.',
                    'Admin.Catalog.Help'
                ),
            ])
            ->add('available_per_user', NumberType::class, [
                'label' => $this->trans('Total available for each user', 'Admin.Catalog.Feature'),
                'help' => $this->trans(
                    'A customer will only be able to use the cart rule "X" time(s).',
                    'Admin.Catalog.Help'
                ),
            ])
            ->add('restrictions', RestrictionsType::class)
        ;
    }
}
