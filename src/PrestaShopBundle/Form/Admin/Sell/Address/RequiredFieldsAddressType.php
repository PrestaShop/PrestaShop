<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Sell\Address;

use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Type\Material\MaterialChoiceTableType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Defines form for address required fields modification (Sell > Customers > Addresses)
 */
class RequiredFieldsAddressType extends AbstractType
{
    /**
     * @var FormChoiceProviderInterface
     */
    private $addressRequiredFieldsChoicesProvider;

    /**
     * @param FormChoiceProviderInterface $addressRequiredFieldsChoicesProvider
     */
    public function __construct(FormChoiceProviderInterface $addressRequiredFieldsChoicesProvider)
    {
        $this->addressRequiredFieldsChoicesProvider = $addressRequiredFieldsChoicesProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('required_fields', MaterialChoiceTableType::class, [
                'label' => false,
                'choices' => $this->addressRequiredFieldsChoicesProvider->getChoices(),
            ])
        ;
    }
}
