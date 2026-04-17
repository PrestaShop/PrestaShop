<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Sell\Order;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\CleanHtml;
use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShopBundle\Translation\TranslatorAwareTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Contracts\Translation\TranslatorInterface;

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
                'display_total' => true,
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
            ->add('value', TextType::class, [
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
