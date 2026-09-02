<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Type;

use PrestaShop\PrestaShop\Core\Form\ChoiceProvider\ShippingInclusionChoiceProvider;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ShippingInclusionChoiceType extends AbstractType
{
    /**
     * @var ShippingInclusionChoiceProvider
     */
    private $shippingInclusionChoiceProvider;

    public function __construct(
        ShippingInclusionChoiceProvider $shippingInclusionChoiceProvider
    ) {
        $this->shippingInclusionChoiceProvider = $shippingInclusionChoiceProvider;
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
            'choices' => $this->shippingInclusionChoiceProvider->getChoices(),
            'placeholder' => false,
            'required' => false,
        ]);
    }
}
