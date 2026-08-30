<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Catalog;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\CleanHtml;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShop\PrestaShop\Core\ConstraintValidator\TypedRegexValidator;
use PrestaShop\PrestaShop\Core\Feature\FeatureInterface;
use PrestaShop\PrestaShop\Core\Form\ChoiceProvider\AttributeGroupChoiceProvider;
use PrestaShopBundle\Form\Admin\Type\ImageWithPreviewType;
use PrestaShopBundle\Form\Admin\Type\ShopChoiceTreeType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Form type for attribute add/edit
 */
class AttributeType extends TranslatorAwareType
{
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        protected readonly AttributeGroupChoiceProvider $attributeGroupChoiceProvider,
        protected readonly FeatureInterface $multistoreFeature
    ) {
        parent::__construct($translator, $locales);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $attributeGroupId = (is_int($options['attribute_group']) && $options['attribute_group'] > 0) ? $options['attribute_group'] : '';

        $builder
            ->add('attribute_group', ChoiceType::class, [
                'label' => $this->trans('Attribute group', 'Admin.Catalog.Feature'),
                'help' => $this->trans('The way the attribute\'s values will be presented to the customers in the product\'s page.', 'Admin.Catalog.Help'),
                'choices' => $this->attributeGroupChoiceProvider->getChoices(),
                'choice_attr' => $this->attributeGroupChoiceProvider->getChoicesAttributes(),
                'data' => $attributeGroupId,
            ])
            ->add('name', TranslatableType::class, [
                'type' => TextType::class,
                'label' => $this->trans('Name', 'Admin.Global'),
                'options' => [
                    'constraints' => [
                        new CleanHtml([
                            'message' => $this->trans('%s is invalid.', 'Admin.Notifications.Error'),
                        ]),
                        new TypedRegex([
                            'type' => TypedRegex::TYPE_CATALOG_NAME,
                        ]),
                    ],
                ],
                'help' => $this->trans('Your internal name for this attribute.', 'Admin.Catalog.Help')
                    . '&nbsp;' . $this->trans('Invalid characters:', 'Admin.Notifications.Info')
                    . ' ' . TypedRegexValidator::CATALOG_CHARS,
            ])
            ->add('color', ColorType::class, [
                'label' => $this->trans('Color', 'Admin.Global'),
                'row_attr' => [
                    'class' => 'js-attribute-type-color-form-row',
                ],
                'required' => false,
            ])
            ->add('texture', ImageWithPreviewType::class, [
                'label' => $this->trans('Texture', 'Admin.Global'),
                'row_attr' => [
                    'class' => 'js-attribute-type-texture-form-row',
                ],
                'required' => false,
                'can_be_deleted' => true,
                'show_size' => true,
            ]);

        if ($this->multistoreFeature->isUsed()) {
            $builder->add('shop_association', ShopChoiceTreeType::class, [
                'label' => $this->trans('Shop association', 'Admin.Global'),
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans(
                            'This field cannot be empty.',
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('attribute_group');
        parent::configureOptions($resolver);
    }
}
