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
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Choice;

class ChangeOrderAddressType extends AbstractType
{
    public const SHIPPING_TYPE = 'shipping';
    public const INVOICE_TYPE = 'invoice';

    /**
     * @var ConfigurableFormChoiceProviderInterface
     */
    private $customerAddressProvider;

    /**
     * @param ConfigurableFormChoiceProviderInterface $customerAddressProvider
     */
    public function __construct(ConfigurableFormChoiceProviderInterface $customerAddressProvider)
    {
        $this->customerAddressProvider = $customerAddressProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('new_address_id', ChoiceType::class, [
                'choices' => $this->customerAddressProvider->getChoices($options),
            ])
            ->add('address_type', HiddenType::class, [
                'constraints' => [
                    new Choice($this->getAvailableAddressTypes()),
                ],
            ]
            );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired([
                'customer_id',
            ])
            ->setAllowedTypes('customer_id', 'int');
    }

    /**
     * @return array
     */
    public function getAvailableAddressTypes()
    {
        return [
            self::SHIPPING_TYPE,
            self::INVOICE_TYPE,
        ];
    }
}
