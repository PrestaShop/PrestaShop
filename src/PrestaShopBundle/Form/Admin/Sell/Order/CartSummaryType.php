<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Sell\Order;

use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Form type for cart summary block of order create page
 */
class CartSummaryType extends AbstractType
{
    /**
     * @var FormChoiceProviderInterface
     */
    private $orderStatesChoiceProvider;

    /**
     * @var FormChoiceProviderInterface
     */
    private $paymentModulesChoiceProvider;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param FormChoiceProviderInterface $orderStatesChoiceProvider
     * @param FormChoiceProviderInterface $paymentModulesChoiceProvider
     * @param TranslatorInterface $translator
     */
    public function __construct(
        FormChoiceProviderInterface $orderStatesChoiceProvider,
        FormChoiceProviderInterface $paymentModulesChoiceProvider,
        TranslatorInterface $translator
    ) {
        $this->orderStatesChoiceProvider = $orderStatesChoiceProvider;
        $this->paymentModulesChoiceProvider = $paymentModulesChoiceProvider;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('cart_id', HiddenType::class, [
                'required' => false,
            ])
            ->add('order_message', TextareaType::class, [
                'required' => false,
            ])
            ->add('payment_module', ChoiceType::class, [
                'choices' => $this->getPaymentModuleChoices(),
                'required' => true,
                'placeholder' => $this->translator->trans(
                    '-- Choose --',
                    [],
                    'Admin.Actions'
                ),
                'autocomplete' => true,
            ])
            ->add('order_state', ChoiceType::class, [
                'choices' => $this->orderStatesChoiceProvider->getChoices(),
                'required' => true,
                'placeholder' => $this->translator->trans(
                    '-- Choose --',
                    [],
                    'Admin.Actions'
                ),
                'autocomplete' => true,
            ]);
    }

    /**
     * Gets payment module choices
     *
     * @return array
     */
    private function getPaymentModuleChoices(): array
    {
        $choices = [];

        foreach ($this->paymentModulesChoiceProvider->getChoices() as $name => $displayName) {
            $choices[$displayName] = $name;
        }

        return $choices;
    }
}
