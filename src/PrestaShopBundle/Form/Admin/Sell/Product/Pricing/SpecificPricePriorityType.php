<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Product\Pricing;

use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SpecificPricePriorityType extends CollectionType
{
    /**
     * @var FormChoiceProviderInterface
     */
    private $priorityChoiceProvider;

    /**
     * @param FormChoiceProviderInterface $priorityChoiceProvider
     */
    public function __construct(
        FormChoiceProviderInterface $priorityChoiceProvider
    ) {
        $this->priorityChoiceProvider = $priorityChoiceProvider;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'entry_type' => ChoiceType::class,
            'entry_options' => [
                'choices' => $this->priorityChoiceProvider->getChoices(),
                'required' => false,
                'placeholder' => false,
                'label' => false,
            ],
            'attr' => [
                'class' => 'specific-price-priorities',
            ],
        ]);
    }
}
