<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Type;

use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class FeatureChoiceType extends TranslatorAwareType
{
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        protected readonly FormChoiceProviderInterface $featureChoiceProvider
    ) {
        parent::__construct($translator, $locales);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => $this->featureChoiceProvider->getChoices(),
            'label' => $this->trans('Feature', 'Admin.Catalog.Feature'),
            'autocomplete' => true,
            'attr' => [
                'class' => 'feature-selector',
            ],
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
