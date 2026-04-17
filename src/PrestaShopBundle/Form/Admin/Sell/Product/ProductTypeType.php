<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Product;

use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use PrestaShop\PrestaShop\Core\Form\FormChoiceAttributeProviderInterface;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductTypeType extends AbstractType
{
    /**
     * @var FormChoiceProviderInterface|FormChoiceAttributeProviderInterface
     */
    private $formChoiceProvider;

    /**
     * @param FormChoiceProviderInterface|FormChoiceAttributeProviderInterface $formChoiceProvider
     */
    public function __construct(
        $formChoiceProvider
    ) {
        $this->formChoiceProvider = $formChoiceProvider;
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => $this->formChoiceProvider->getChoices(),
            'choice_attr' => $this->formChoiceProvider->getChoicesAttributes(),
            'required' => true,
            'label' => false,
            'empty_data' => ProductType::TYPE_STANDARD,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'product_type';
    }
}
