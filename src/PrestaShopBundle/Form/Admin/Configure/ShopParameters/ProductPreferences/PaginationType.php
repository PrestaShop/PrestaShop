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

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\ProductPreferences;

use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Type;

/**
 * Class generates "Pagination" form
 * in "Configure > Shop Parameters > Product Settings" page.
 */
class PaginationType extends TranslatorAwareType
{
    public const FIELD_PRODUCTS_PER_PAGE = 'products_per_page';
    public const FIELD_DEFAULT_ORDER_BY = 'default_order_by';
    public const FIELD_DEFAULT_ORDER_WAY = 'default_order_way';
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(static::FIELD_PRODUCTS_PER_PAGE, IntegerType::class, [
                'label' => $this->trans('Products per page', 'Admin.Shopparameters.Feature'),
                'help' => $this->trans('Number of products displayed per page. Default is 10.', 'Admin.Shopparameters.Help'),
                'constraints' => [
                    new Type(
                        [
                            'value' => 'numeric',
                            'message' => $this->trans('The field is invalid. Please enter a positive integer.', 'Admin.Notifications.Error'),
                        ]
                    ),
                    new GreaterThanOrEqual(
                        [
                            'value' => 0,
                            'message' => $this->trans('The field is invalid. Please enter a positive integer.', 'Admin.Notifications.Error'),
                        ]
                    ),
                ],
            ])
            ->add(static::FIELD_DEFAULT_ORDER_BY, ChoiceType::class, [
                'label' => $this->trans('Default order by', 'Admin.Shopparameters.Feature'),
                'help' => $this->trans('The order in which products are displayed in the product list.', 'Admin.Shopparameters.Help'),
                'choices' => [
                    'Product name' => 0,
                    'Product price' => 1,
                    'Product add date' => 2,
                    'Product modified date' => 3,
                    'Position inside category' => 4,
                    'Brand' => 5,
                    'Product quantity' => 6,
                    'Product reference' => 7,
                ],
                'required' => true,
            ])
            ->add(static::FIELD_DEFAULT_ORDER_WAY, ChoiceType::class, [
                'label' => $this->trans('Default order method', 'Admin.Shopparameters.Feature'),
                'help' => $this->trans('Default order method for product list.', 'Admin.Shopparameters.Help'),
                'choices' => [
                    'Ascending' => 0,
                    'Descending' => 1,
                ],
                'required' => true,
                'choice_translation_domain' => 'Admin.Global',
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
        return 'product_preferences_pagination_block';
    }
}
