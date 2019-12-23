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

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\CleanHtml;
use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShopBundle\Translation\TranslatorAwareTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\Type;

class AddOrderCartRuleType extends AbstractType
{
    use TranslatorAwareTrait;

    /**
     * @var FormChoiceProviderInterface
     */
    private $orderDiscountTypeChoiceProvider;

    /**
     * @var ConfigurableFormChoiceProviderInterface
     */
    private $orderInvoiceByIdChoiceProvider;

    /**
     * @var int
     */
    private $contextLangId;

    /**
     * @param FormChoiceProviderInterface $orderDiscountTypeChoiceProvider
     * @param ConfigurableFormChoiceProviderInterface $orderInvoiceByIdChoiceProvider
     * @param int $contextLangId
     * @param TranslatorInterface $translator
     */
    public function __construct(
        FormChoiceProviderInterface $orderDiscountTypeChoiceProvider,
        ConfigurableFormChoiceProviderInterface $orderInvoiceByIdChoiceProvider,
        int $contextLangId,
        TranslatorInterface $translator
    ) {
        $this->orderDiscountTypeChoiceProvider = $orderDiscountTypeChoiceProvider;
        $this->orderInvoiceByIdChoiceProvider = $orderInvoiceByIdChoiceProvider;
        $this->contextLangId = $contextLangId;
        $this->setTranslator($translator);
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
            ]) : [];

        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new CleanHtml([
                        'message' => $this->trans(
                            'Cart rule name must contain clean HTML',
                            [],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
            ])
            ->add('type', ChoiceType::class, [
                'choices' => $this->orderDiscountTypeChoiceProvider->getChoices(),
            ])
            ->add('value', NumberType::class, [
                'attr' => [
                    'step' => 0.01,
                ],
                'constraints' => new Type([
                    'type' => 'numeric',
                    'message' => $this->trans('Discount value must be a number', [], 'Admin.Notifications.Error'),
                ]),
            ])
            ->add('invoice_id', ChoiceType::class, [
                'choices' => $invoices,
                'required' => false,
                'placeholder' => false,
            ])
            ->add('apply_on_all_invoices', CheckboxType::class, [
                'required' => false,
                'label' => $this->trans('Apply on all invoices', [], 'Admin.Orderscustomers.Feature'),
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'order_id' => null,
            ])
            ->setAllowedTypes('order_id', ['int', 'null'])
        ;
    }
}
