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

namespace PrestaShopBundle\Form\Admin\Sell\Discount;

use PrestaShopBundle\Form\Admin\Type\ToggleChildrenChoiceType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\When;

class CartConditionsType extends TranslatorAwareType
{
    public const NONE = 'none';
    public const MINIMUM_AMOUNT = 'minimum_amount';
    public const MINIMUM_PRODUCT_QUANTITY = 'minimum_product_quantity';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add(self::NONE, HiddenType::class, [
                'label' => $this->trans('None', 'Admin.Catalog.Feature'),
            ])
            ->add(self::MINIMUM_AMOUNT, MinimumAmountType::class, [
                'label' => $this->trans('Minimum purchase amount', 'Admin.Catalog.Feature'),
                'required' => false,
                'constraints' => [
                    new When(
                        expression: sprintf(
                            'this.getParent().get("children_selector").getData() === "%s"',
                            self::MINIMUM_AMOUNT
                        ),
                        constraints: new Collection(
                            fields: [
                                'value' => new GreaterThan(0),
                            ],
                            allowExtraFields: true,
                        ),
                    ),
                ],
            ])
            ->add(self::MINIMUM_PRODUCT_QUANTITY, IntegerType::class, [
                'label' => $this->trans('Minimum product quantity', 'Admin.Catalog.Feature'),
                'required' => false,
                'default_empty_data' => 0,
                'constraints' => [
                    new When(
                        expression: sprintf(
                            'this.getParent().get("children_selector").getData() === "%s"',
                            self::MINIMUM_PRODUCT_QUANTITY
                        ),
                        constraints: [
                            new GreaterThan(0),
                        ],
                    ),
                ],
            ])
        ;
    }

    public function getParent()
    {
        return ToggleChildrenChoiceType::class;
    }
}
