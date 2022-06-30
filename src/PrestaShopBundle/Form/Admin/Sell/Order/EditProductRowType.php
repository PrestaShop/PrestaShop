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

namespace PrestaShopBundle\Form\Admin\Sell\Order;

use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

class EditProductRowType extends TranslatorAwareType
{
    /**
     * @var ConfigurableFormChoiceProviderInterface
     */
    private $orderInvoiceByIdChoiceProvider;

    /**
     * @var int
     */
    private $contextLangId;

    /**
     * EditProductRowType constructor.
     *
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param ConfigurableFormChoiceProviderInterface $orderInvoiceByIdChoiceProvider
     * @param int $contextLangId
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        ConfigurableFormChoiceProviderInterface $orderInvoiceByIdChoiceProvider,
        int $contextLangId
    ) {
        parent::__construct($translator, $locales);

        $this->orderInvoiceByIdChoiceProvider = $orderInvoiceByIdChoiceProvider;
        $this->contextLangId = $contextLangId;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $invoices = $options['order_id'] ?
            $this->orderInvoiceByIdChoiceProvider->getChoices([
                'id_order' => $options['order_id'],
                'id_lang' => $this->contextLangId,
                'display_total' => false,
            ]) : [];

        $builder
            ->add('price_tax_excluded', NumberType::class, [
                'label' => false,
                'unit' => sprintf('%s %s',
                    $options['symbol'],
                    $this->trans('tax excl.', 'Admin.Global')
                ),
                'attr' => [
                    'class' => 'editProductPriceTaxExcl',
                ],
            ])
            ->add('price_tax_included', NumberType::class, [
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
            ->add('invoice', ChoiceType::class, [
                'choices' => $invoices,
                'label' => false,
                'attr' => [
                    'class' => 'editProductInvoice custom-select',
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
