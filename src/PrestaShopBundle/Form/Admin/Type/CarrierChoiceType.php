<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Type;

use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShop\PrestaShop\Core\Image\ImageProviderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CarrierChoiceType extends AbstractType
{
    public function __construct(
        private readonly FormChoiceProviderInterface $carrierChoiceProvider,
        private readonly ImageProviderInterface $carrierLogoProvider,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $carriers = $this->carrierChoiceProvider->getChoices();
        $resolver->setDefaults([
            'choices' => $carriers,
            'choice_attr' => $this->formatChoicesAttr($carriers),
        ]);
    }

    private function formatChoicesAttr(array $carriers): array
    {
        $choicesAttr = [];
        foreach ($carriers as $name => $id) {
            $choicesAttr[$name] = [
                'data-logo' => $this->carrierLogoProvider->getPath($id),
            ];
        }

        return $choicesAttr;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
