<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Sell\Discount;

use PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider\FeaturesChoiceProvider;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class DiscountFeaturesType extends AbstractType
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly FeaturesChoiceProvider $featuresChoiceProvider,
    ) {
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
        $feature = $this->featuresChoiceProvider->getChoices();
        $choices = array_merge([
            $this->trans('No Features', 'Admin.Catalog.Feature') => 0,
        ], $feature);

        $resolver->setDefaults([
            'label' => $this->trans('Features', 'Admin.Catalog.Feature'),
            'label_tag_name' => 'h3',
            'required' => false,
            // placeholder false is important to avoid empty option in select input despite required being false
            'placeholder' => false,
            'choices' => $choices,
            'autocomplete' => true,
        ]);
    }

    protected function trans(string $key, string $domain, array $parameters = []): string
    {
        return $this->translator->trans($key, $parameters, $domain);
    }
}
