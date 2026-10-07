<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Product\Combination;

use PrestaShop\PrestaShop\Core\Domain\Product\ProductSettings;
use PrestaShopBundle\Form\Admin\Type\FormattedTextareaType;
use PrestaShopBundle\Form\Admin\Type\TextWithLengthCounterType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Localized values overriding the product ones, left empty to keep the product value
 */
final class CombinationContentType extends TranslatorAwareType
{
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        private readonly int $shortDescriptionMaxLength,
    ) {
        parent::__construct($translator, $locales);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description_short', TranslatableType::class, [
                'label' => $this->trans('Summary', 'Admin.Global'),
                'help' => $this->trans('Leave empty to use the product summary.', 'Admin.Catalog.Help'),
                'type' => FormattedTextareaType::class,
                'options' => [
                    'limit' => $this->shortDescriptionMaxLength ?: ProductSettings::MAX_DESCRIPTION_SHORT_LENGTH,
                ],
                'modify_all_shops' => true,
            ])
            ->add('description', TranslatableType::class, [
                'label' => $this->trans('Description', 'Admin.Global'),
                'help' => $this->trans('Leave empty to use the product description.', 'Admin.Catalog.Help'),
                'type' => FormattedTextareaType::class,
                'options' => [
                    'limit' => ProductSettings::MAX_DESCRIPTION_LENGTH,
                ],
                'modify_all_shops' => true,
            ])
            ->add('meta_title', TranslatableType::class, [
                'label' => $this->trans('Meta title', 'Admin.Catalog.Feature'),
                'help' => $this->trans('Leave empty to use the product meta title, followed by the combination attributes when enabled in Traffic & SEO.', 'Admin.Catalog.Help'),
                'type' => TextWithLengthCounterType::class,
                'options' => $this->getLengthCounterOptions('text', ProductSettings::MAX_META_TITLE_LENGTH),
                'modify_all_shops' => true,
            ])
            ->add('meta_description', TranslatableType::class, [
                'label' => $this->trans('Meta description', 'Admin.Catalog.Feature'),
                'help' => $this->trans('Leave empty to use the product meta description.', 'Admin.Catalog.Help'),
                'type' => TextWithLengthCounterType::class,
                'options' => $this->getLengthCounterOptions('textarea', ProductSettings::MAX_META_DESCRIPTION_LENGTH),
                'modify_all_shops' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'label' => $this->trans('Description and SEO', 'Admin.Catalog.Feature'),
            'label_tag_name' => 'h3',
            'required' => false,
        ]);
    }

    private function getLengthCounterOptions(string $input, int $maxLength): array
    {
        return [
            'input' => $input,
            'max_length' => $maxLength,
            'position' => 'after',
            'constraints' => [
                new Length([
                    'max' => $maxLength,
                    'maxMessage' => $this->trans(
                        'This field cannot be longer than %limit% characters.',
                        'Admin.Notifications.Error',
                        ['%limit%' => $maxLength]
                    ),
                ]),
            ],
        ];
    }
}
