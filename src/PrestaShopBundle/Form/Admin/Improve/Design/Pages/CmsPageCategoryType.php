<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Improve\Design\Pages;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\CleanHtml;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DefaultLanguage;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\IsUrlRewrite;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShop\PrestaShop\Core\ConstraintValidator\TypedRegexValidator;
use PrestaShopBundle\Form\Admin\Type\Material\MaterialChoiceTreeType;
use PrestaShopBundle\Form\Admin\Type\ShopChoiceTreeType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Defines form for Improve > Design > Pages > Categories create/edit actions
 */
class CmsPageCategoryType extends TranslatorAwareType
{
    public const NAME_MAX_LENGTH = 64;
    public const META_TITLE_MAX_LENGTH = 255;
    public const META_DESCRIPTION_MAX_LENGTH = 512;

    /**
     * @var array
     */
    private $allCmsCategories;

    /**
     * @var bool
     */
    private $isShopFeatureEnabled;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param array $allCmsCategories
     * @param bool $isShopFeatureEnabled
     */
    public function __construct(TranslatorInterface $translator, array $locales, array $allCmsCategories, $isShopFeatureEnabled)
    {
        parent::__construct($translator, $locales);
        $this->allCmsCategories = $allCmsCategories;
        $this->isShopFeatureEnabled = $isShopFeatureEnabled;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $invalidCharactersForCatalogLabel = $this->trans('Invalid characters:', 'Admin.Global') . TypedRegexValidator::CATALOG_CHARS;
        $invalidCharactersForNameLabel = $this->trans('Invalid characters:', 'Admin.Global') . TypedRegexValidator::GENERIC_NAME_CHARS;
        $builder
            ->add('name', TranslatableType::class, [
                'label' => $this->trans('Name', 'Admin.Global'),
                'help' => $invalidCharactersForCatalogLabel,
                'constraints' => [
                    new DefaultLanguage(),
                ],
                'options' => [
                    'constraints' => [
                        new TypedRegex([
                            'type' => 'catalog_name',
                        ]),
                        new Length([
                            'max' => self::NAME_MAX_LENGTH,
                            'maxMessage' => $this->trans(
                                'This field cannot be longer than %limit% characters',
                                'Admin.Notifications.Error',
                                ['%limit%' => self::NAME_MAX_LENGTH]
                            ),
                        ]),
                    ],
                ],
            ])
            ->add('is_displayed', SwitchType::class, [
                'label' => $this->trans('Displayed', 'Admin.Global'),
                'required' => false,
            ])
            ->add('parent_category', MaterialChoiceTreeType::class, [
                'label' => $this->trans('Parent category', 'Admin.Design.Feature'),
                'required' => false,
                'choices_tree' => $this->allCmsCategories,
                'choice_value' => 'id_cms_category',
            ])
            ->add('description', TranslatableType::class, [
                'label' => $this->trans('Description', 'Admin.Global'),
                'required' => false,
                'type' => TextareaType::class,
                'options' => [
                    'constraints' => [
                        new CleanHtml(),
                    ],
                ],
            ])
            ->add('meta_title', TranslatableType::class, [
                'label' => $this->trans('Meta title', 'Admin.Global'),
                'required' => false,
                'help' => $invalidCharactersForNameLabel,
                'options' => [
                    'constraints' => [
                        new TypedRegex([
                            'type' => 'generic_name',
                        ]),
                        new Length([
                            'max' => self::META_TITLE_MAX_LENGTH,
                            'maxMessage' => $this->trans(
                                'This field cannot be longer than %limit% characters',
                                'Admin.Notifications.Error',
                                ['%limit%' => self::META_TITLE_MAX_LENGTH]
                            ),
                        ]),
                    ],
                ],
            ])
            ->add('meta_description', TranslatableType::class, [
                'label' => $this->trans('Meta description', 'Admin.Global'),
                'help' => $invalidCharactersForNameLabel,
                'required' => false,
                'options' => [
                    'constraints' => [
                        new TypedRegex([
                            'type' => 'generic_name',
                        ]),
                        new Length([
                            'max' => self::META_DESCRIPTION_MAX_LENGTH,
                            'maxMessage' => $this->trans(
                                'This field cannot be longer than %limit% characters',
                                'Admin.Notifications.Error',
                                ['%limit%' => self::META_DESCRIPTION_MAX_LENGTH]
                            ),
                        ]),
                    ],
                ],
            ])
            ->add('friendly_url', TranslatableType::class, [
                'label' => $this->trans('Friendly URL', 'Admin.Global'),
                'help' => $this->trans('Unless the \'Accented URL\' option is enabled (in Shop parameters > Traffic & SEO), only letters, numbers, underscores (_), and hyphens (-) are allowed.', 'Admin.Catalog.Help'),
                'constraints' => [
                    new DefaultLanguage(),
                ],
                'options' => [
                    'constraints' => [
                        new IsUrlRewrite(),
                        new Length([
                            'max' => self::NAME_MAX_LENGTH,
                            'maxMessage' => $this->trans(
                                'This field cannot be longer than %limit% characters',
                                'Admin.Notifications.Error',
                                ['%limit%' => self::NAME_MAX_LENGTH]
                            ),
                        ]),
                    ],
                ],
            ])
        ;

        if ($this->isShopFeatureEnabled) {
            $builder->add('shop_association', ShopChoiceTreeType::class, [
                'label' => $this->trans('Store association', 'Admin.Global'),
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans(
                            'The %s field is required.',
                            'Admin.Notifications.Error',
                            [
                                sprintf('"%s"', $this->trans('Store association', 'Admin.Global')),
                            ]
                        ),
                    ]),
                ],
            ]);
        }
    }
}
