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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShopBundle\Form\Admin\Sell\Order;

use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Type\AmountCurrencyType;
use PrestaShopBundle\Form\Admin\Type\DatePickerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Contracts\Translation\TranslatorInterface;

class OrderPaymentType extends AbstractType
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var ConfigurableFormChoiceProviderInterface
     */
    private $currencySymbolByIdChoiceProvider;

    /**
     * @var ConfigurableFormChoiceProviderInterface
     */
    private $orderInvoiceChoiceProvider;

    /**
     * @var int
     */
    private $contextLanguageId;

    /**
     * @var int
     */
    private $contextShopId;

    /**
     * @var FormChoiceProviderInterface
     */
    private $installedPaymentModulesChoiceProvider;

    /**
     * @param ConfigurableFormChoiceProviderInterface $currencySymbolByIdChoiceProvider
     * @param ConfigurableFormChoiceProviderInterface $orderInvoiceChoiceProvider
     * @param FormChoiceProviderInterface $installedPaymentModulesChoiceProvider
     * @param int $contextLanguageId
     * @param int $contextShopId
     */
    public function __construct(
        TranslatorInterface $translator,
        ConfigurableFormChoiceProviderInterface $currencySymbolByIdChoiceProvider,
        ConfigurableFormChoiceProviderInterface $orderInvoiceChoiceProvider,
        FormChoiceProviderInterface $installedPaymentModulesChoiceProvider,
        int $contextLanguageId,
        int $contextShopId
    ) {
        $this->translator = $translator;
        $this->currencySymbolByIdChoiceProvider = $currencySymbolByIdChoiceProvider;
        $this->orderInvoiceChoiceProvider = $orderInvoiceChoiceProvider;
        $this->contextLanguageId = $contextLanguageId;
        $this->contextShopId = $contextShopId;
        $this->installedPaymentModulesChoiceProvider = $installedPaymentModulesChoiceProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date', DatePickerType::class, [
                'date_format' => 'YYYY-MM-DD H:m:s',
            ])
            ->add('payment_method', TextType::class, [
                'empty_data' => '',
                'data_list' => $this->installedPaymentModulesChoiceProvider->getChoices(),
            ])
            ->add('transaction_id', TextType::class, [
                'required' => false,
            ])
            ->add('amount_currency', AmountCurrencyType::class, [
                'amount_constraints' => [
                    new NotNull(),
                    new GreaterThan([
                        'value' => 0,
                        'message' => $this->translator->trans(
                            'Invalid value: the payment must be a positive amount.', [], 'Admin.Notifications.Error'
                        ),
                    ]),
                ],
                'currencies' => $this->currencySymbolByIdChoiceProvider->getChoices([
                    'id_shop' => $this->contextShopId,
                ]),
            ])
            ->add('id_invoice', ChoiceType::class, [
                'choices' => $this->orderInvoiceChoiceProvider->getChoices([
                    'id_order' => $options['id_order'],
                    'id_lang' => $this->contextLanguageId,
                ]),
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired([
                'id_order',
            ])
            ->setAllowedTypes('id_order', 'int')
        ;
    }
}
