<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
