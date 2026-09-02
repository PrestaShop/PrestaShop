<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Sell\Customer;

use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerDeleteMethod;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class DeleteCustomersType
 */
class DeleteCustomersType extends AbstractType
{
    /**
     * @var array
     */
    private $customerDeleteMethodChoices;

    /**
     * @param array $customerDeleteMethodChoices
     */
    public function __construct(array $customerDeleteMethodChoices)
    {
        $this->customerDeleteMethodChoices = $customerDeleteMethodChoices;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('delete_method', ChoiceType::class, [
                'choices' => $this->customerDeleteMethodChoices,
                'expanded' => true,
                'multiple' => false,
                'translation_domain' => false,
                'data' => CustomerDeleteMethod::ALLOW_CUSTOMER_REGISTRATION,
            ])
            ->add('customers_to_delete', CollectionType::class, [
                'entry_type' => HiddenType::class,
                'label' => false,
                'allow_add' => true,
            ])
        ;
    }
}
