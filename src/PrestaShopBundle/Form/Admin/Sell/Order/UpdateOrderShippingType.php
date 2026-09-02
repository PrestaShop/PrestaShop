<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Sell\Order;

use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UpdateOrderShippingType extends AbstractType
{
    /**
     * @var ConfigurableFormChoiceProviderInterface
     */
    private $carrierForOrderChoiceProvider;

    /**
     * @param ConfigurableFormChoiceProviderInterface $carrierForOrderChoiceProvider
     */
    public function __construct(ConfigurableFormChoiceProviderInterface $carrierForOrderChoiceProvider)
    {
        $this->carrierForOrderChoiceProvider = $carrierForOrderChoiceProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('new_carrier_id', ChoiceType::class, [
                'choices' => $this->carrierForOrderChoiceProvider->getChoices([
                    'order_id' => $options['order_id'],
                ]),
                'autocomplete' => true,
            ])
            ->add('current_order_carrier_id', HiddenType::class)
            ->add('tracking_number', TextType::class, [
                'required' => false,
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
                'order_id',
            ])
            ->setAllowedTypes('order_id', 'int')
        ;
    }
}
