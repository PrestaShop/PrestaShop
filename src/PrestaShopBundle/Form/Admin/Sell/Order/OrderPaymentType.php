<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
