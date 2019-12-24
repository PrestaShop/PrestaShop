<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Form\Admin\Sell\Order;

use PrestaShopBundle\Form\Admin\Type\TextWithUnitType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditProductRowType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('price_tax_excluded', TextWithUnitType::class, [
                'label' => false,
                'unit' => sprintf('%s %s',
                    $options['symbol'],
                    $this->trans('tax excl.', 'Admin.Global')
                ),
                'attr' => [
                    'class' => 'editProductPriceTaxExcl',
                ],
            ])
            ->add('price_tax_included', TextWithUnitType::class, [
                'label' => false,
                'unit' => sprintf('%s %s',
                    $options['symbol'],
                    $this->trans('tax incl.', 'Admin.Global')
                ),
                'attr' => [
                    'class' => 'editProductPriceTaxIncl',
                ],
            ])
            ->add('quantity', NumberType::class, [
                'label' => false,
                'data' => 1,
                'scale' => 0,
                'attr' => [
                    'min' => 1,
                    'class' => 'editProductQuantity',
                ],
            ])
            ->add('cancel', ButtonType::class, [
                'label' => $this->trans('Cancel', 'Admin.Actions'),
                'attr' => [
                    'class' => 'btn btn-sm btn-secondary js-product-edit-action-btn mr-2 mt-2 mb-2 productEditCancelBtn',
                ],
            ])
            ->add('save', ButtonType::class, [
                'label' => $this->trans('Save', 'Admin.Actions'),
                'disabled' => true,
                'attr' => [
                    'class' => 'btn btn-sm btn-primary js-product-edit-action-btn mt-2 mb-2 productEditSaveBtn',
                    'data-order-id' => $options['order_id'],
                    'data-update-message' => $this->trans('Are you sure?', 'Admin.Notifications.Warning'),
                ],
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired(['symbol'])
            ->setDefaults([
                'order_id' => null,
            ])
            ->setAllowedTypes('order_id', ['int', 'null'])
            ->setAllowedTypes('symbol', ['string'])
        ;
    }
}
