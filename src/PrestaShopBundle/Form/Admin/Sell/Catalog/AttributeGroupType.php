<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Catalog;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\CleanHtml;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DefaultLanguage;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShop\PrestaShop\Core\ConstraintValidator\TypedRegexValidator;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\ValueObject\AttributeGroupType as GroupType;
use PrestaShop\PrestaShop\Core\Feature\FeatureInterface;
use PrestaShopBundle\Form\Admin\Type\ShopChoiceTreeType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Form type for attribute add/edit
 */
class AttributeGroupType extends TranslatorAwareType
{
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        protected FeatureInterface $multistoreFeature
    ) {
        parent::__construct($translator, $locales);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TranslatableType::class, [
                'type' => TextType::class,
                'label' => $this->trans('Name', 'Admin.Global'),
                'constraints' => [
                    new DefaultLanguage(),
                ],
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
            ->add('public_name', TranslatableType::class, [
                'type' => TextType::class,
                'label' => $this->trans('Public name', 'Admin.Catalog.Feature'),
                'constraints' => [
                    new DefaultLanguage(),
                ],
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
                'help' => $this->trans('The public name for this attribute, displayed to the customers.', 'Admin.Catalog.Help')
                    . '&nbsp;' . $this->trans('Invalid characters:', 'Admin.Notifications.Info')
                    . ' ' . TypedRegexValidator::CATALOG_CHARS,
            ])
            ->add('group_type', ChoiceType::class, [
                'label' => $this->trans('Attribute type', 'Admin.Catalog.Feature'),
                'help' => $this->trans('The way the attribute\'s values will be presented to the customers in the product\'s page.', 'Admin.Catalog.Help'),
                'choices' => [
                    $this->trans('Drop-down list', 'Admin.Global') => GroupType::ATTRIBUTE_GROUP_TYPE_SELECT,
                    $this->trans('Radio buttons', 'Admin.Global') => GroupType::ATTRIBUTE_GROUP_TYPE_RADIO,
                    $this->trans('Color or texture', 'Admin.Catalog.Feature') => GroupType::ATTRIBUTE_GROUP_TYPE_COLOR,
                ],
            ])
        ;

        if ($this->multistoreFeature->isUsed()) {
            $builder->add('shop_association', ShopChoiceTreeType::class, [
                'label' => $this->trans('Shop association', 'Admin.Global'),
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans(
                            'This field cannot be empty.', 'Admin.Notifications.Error'
                        ),
                    ]),
                ],
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'requiredFields' => [],
        ]);
    }
}
