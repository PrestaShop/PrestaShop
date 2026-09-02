<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Product\Combination;

use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class CombinationImagesChoiceType extends TranslatorAwareType
{
    /**
     * @var ConfigurableFormChoiceProviderInterface
     */
    private $imagesChoiceProvider;

    /**
     * @param TranslatorInterface $translator
     * @param array<int, array<string, mixed>> $locales
     * @param ConfigurableFormChoiceProviderInterface $imagesChoiceProvider
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        ConfigurableFormChoiceProviderInterface $imagesChoiceProvider
    ) {
        parent::__construct($translator, $locales);
        $this->imagesChoiceProvider = $imagesChoiceProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver
            ->setRequired([
                'product_id',
            ])
            ->setAllowedTypes('product_id', 'int')
            ->setDefaults([
                'label' => $this->trans('Images', 'Admin.Global'),
                'label_subtitle' => $this->trans('You can specify which images should be displayed when customer selects this combination. If you don\'t select any image, all will be displayed. The default image of the combination will be the first one selected from the list.', 'Admin.Catalog.Feature'),
                'choice_attr' => function (string $choice, string $key): array {
                    return ['data-image-url' => $key];
                },
                'multiple' => true,
                'expanded' => true,
            ]
            );

        $choiceProvider = $this->imagesChoiceProvider;
        $resolver->setNormalizer('choices', function (OptionsResolver $resolver) use ($choiceProvider) {
            $productId = $resolver->offsetGet('product_id');

            return $choiceProvider->getChoices(['product_id' => $productId]);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
