<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Sell\Customer;

use PrestaShopBundle\Form\Admin\Type\Material\MaterialChoiceTableType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Defines form for customer required fields
 */
class RequiredFieldsType extends AbstractType
{
    /**
     * @var array
     */
    private $customerRequiredFieldsChoices;

    /**
     * @param array $customerRequiredFieldsChoices
     */
    public function __construct(array $customerRequiredFieldsChoices)
    {
        $this->customerRequiredFieldsChoices = $customerRequiredFieldsChoices;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('required_fields', MaterialChoiceTableType::class, [
                'label' => false,
                'choices' => $this->customerRequiredFieldsChoices,
            ])
        ;
    }
}
