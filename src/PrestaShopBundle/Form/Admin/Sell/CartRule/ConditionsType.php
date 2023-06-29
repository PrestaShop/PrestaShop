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

use PrestaShopBundle\Form\Admin\Type\CustomerSearchType;
use PrestaShopBundle\Form\Admin\Type\DateRangeType;
use PrestaShopBundle\Form\Admin\Type\ShopChoiceTreeType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;

class ConditionsType extends TranslatorAwareType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('customer', CustomerSearchType::class, [
                'disabling_switch_event' => 'switchCartRuleCustomer',
            ])
            ->add('valid_date_range', DateRangeType::class, [
                'label' => false,
                'label_from' => $this->trans('Valid from', 'Admin.Catalog.Feature'),
                'label_to' => $this->trans('Valid to', 'Admin.Catalog.Feature'),
                'required' => false,
                'date_format' => DateRangeType::DEFAULT_DATE_TIME_FORMAT,
            ])
            ->add('minimum_amount', MinimumAmountType::class)
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
            ->add('shop_association', ShopChoiceTreeType::class, [
                'label' => $this->trans('Store association', 'Admin.Global'),
            ])
            //@todo: Restrictions not handled. Will be in separate PR.
        ;
    }
}
