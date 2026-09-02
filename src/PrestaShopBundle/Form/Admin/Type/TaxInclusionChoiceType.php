<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Type;

use PrestaShop\PrestaShop\Core\Form\ChoiceProvider\TaxInclusionChoiceProvider;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaxInclusionChoiceType extends AbstractType
{
    /**
     * @var TaxInclusionChoiceProvider
     */
    private $taxInclusionChoiceProvider;

    public function __construct(
        TaxInclusionChoiceProvider $shippingInclusionChoiceProvider
    ) {
        $this->taxInclusionChoiceProvider = $shippingInclusionChoiceProvider;
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => false,
            'choices' => $this->taxInclusionChoiceProvider->getChoices(),
            'placeholder' => false,
            'required' => false,
            'row_attr' => [
                'class' => 'js-include-tax-row',
            ],
        ]);
    }
}
