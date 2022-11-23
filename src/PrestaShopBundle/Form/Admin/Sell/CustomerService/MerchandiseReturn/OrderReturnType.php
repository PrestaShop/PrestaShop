<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\CustomerService\MerchandiseReturn;

use PrestaShopBundle\Form\Admin\Type\LinkPreviewType;
use PrestaShopBundle\Form\Admin\Type\TextPreviewType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Form type for order returns options
 */
class OrderReturnType extends TranslatorAwareType
{
    private const CUSTOMER_NAME_PLACEHOLDER = '___customer_name___';
    private const CUSTOMER_LINK_PLACEHOLDER = '___customer_link___';
    private const ORDER_PLACEHOLDER = '___order___';
    private const ORDER_LINK_PLACEHOLDER = '___order_link___';
    private const QUESTION_PLACEHOLDER = '___question___';

    /**
     * @var array
     */
    private $stateChoices;

    /**
     * OrderReturnType constructor.
     *
     * @param array $stateChoices
     * @param TranslatorInterface $translator
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
                'default_empty_data' => static::CUSTOMER_NAME_PLACEHOLDER,
                'label' => $this->trans('Customer', 'Admin.Global'),
            ])
            ->add('customer_link', LinkPreviewType::class, [
                'default_empty_data' => static::CUSTOMER_LINK_PLACEHOLDER,
                'button_label' => $this->trans('View customer details', 'Admin.Actions'),
                'attr' => [
                    'class' => 'btn btn-outline-secondary',
                ],
            ])
            ->add('order', TextPreviewType::class, [
                'default_empty_data' => static::ORDER_PLACEHOLDER,
                'label' => $this->trans('Order', 'Admin.Global'),
            ])
            ->add('order_link', LinkPreviewType::class, [
                'default_empty_data' => static::ORDER_LINK_PLACEHOLDER,
                'button_label' => $this->trans('View order details', 'Admin.Actions'),
                'attr' => [
                    'class' => 'btn btn-outline-secondary',
                ],
            ])
            ->add('question', TextPreviewType::class, [
                'default_empty_data' => static::QUESTION_PLACEHOLDER,
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
