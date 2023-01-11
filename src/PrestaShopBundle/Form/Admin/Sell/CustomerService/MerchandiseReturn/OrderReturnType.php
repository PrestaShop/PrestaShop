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

namespace PrestaShopBundle\Form\Admin\Sell\CustomerService\MerchandiseReturn;

use PrestaShopBundle\Form\Admin\Type\LinkPreviewType;
use PrestaShopBundle\Form\Admin\Type\TextPreviewType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Form type for order returns options
 */
class OrderReturnType extends TranslatorAwareType
{
    /**
     * @var array
     */
    private $stateChoices;

    /**
     * OrderReturnType constructor.
     *
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param array $stateChoices
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        array $stateChoices
    ) {
        parent::__construct($translator, $locales);
        $this->stateChoices = $stateChoices;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('customer_name', TextPreviewType::class, [
                'label' => $this->trans('Customer', 'Admin.Global'),
            ])
            ->add('customer_link', LinkPreviewType::class, [
                'button_label' => $this->trans('View customer details', 'Admin.Actions'),
                'attr' => [
                    'class' => 'btn btn-outline-secondary',
                ],
            ])
            ->add('order', TextPreviewType::class, [
                'label' => $this->trans('Order', 'Admin.Global'),
            ])
            ->add('order_link', LinkPreviewType::class, [
                'button_label' => $this->trans('View order details', 'Admin.Actions'),
                'attr' => [
                    'class' => 'btn btn-outline-secondary',
                ],
            ])
            ->add('question', TextPreviewType::class, [
                'allow_html' => true,
                'label' => $this->trans('Customer explanation', 'Admin.Orderscustomers.Feature'),
            ])
            ->add('order_return_state', ChoiceType::class, [
                'required' => true,
                'choices' => $this->stateChoices,
                'label' => $this->trans('Status', 'Admin.Global'),
            ])
        ;
    }
}
