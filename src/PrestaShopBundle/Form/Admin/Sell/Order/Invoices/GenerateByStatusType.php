<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Sell\Order\Invoices;

use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class generates "By order status" form
 * in "Sell > Orders > Invoices" page.
 */
class GenerateByStatusType extends TranslatorAwareType
{
    /**
     * @var array
     */
    private $orderCountsByState;
    /**
     * @var FormChoiceProviderInterface
     */
    private $orderStateChoiceProvider;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param FormChoiceProviderInterface $orderStateChoiceProvider
     * @param array $orderCountsByState
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        FormChoiceProviderInterface $orderStateChoiceProvider,
        array $orderCountsByState
    ) {
        parent::__construct($translator, $locales);
        $this->orderCountsByState = $orderCountsByState;
        $this->orderStateChoiceProvider = $orderStateChoiceProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('order_states', ChoiceType::class, [
                'label' => $this->trans('Order statuses', 'Admin.Orderscustomers.Feature'),
                'help' => $this->trans('You can also export orders which have not been charged yet.', 'Admin.Orderscustomers.Help'),
                'expanded' => true,
                'multiple' => true,
                'choices' => $this->orderStateChoiceProvider->getChoices(),
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        /** @var FormView $child */
        foreach ($view->children['order_states'] as $child) {
            $child->vars['orders_count'] = 0;

            if (array_key_exists($child->vars['value'], $this->orderCountsByState)) {
                $child->vars['orders_count'] = $this->orderCountsByState[$child->vars['value']];
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'Admin.Orderscustomers.Feature',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'orders_invoices_by_status_block';
    }
}
