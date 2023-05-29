<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShopBundle\Form\Admin\Improve\Design\Pages;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\CleanHtml;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DefaultLanguage;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\IsUrlRewrite;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShopBundle\Form\Admin\Type\CustomContentType;
use PrestaShopBundle\Form\Admin\Type\FormattedTextareaType;
use PrestaShopBundle\Form\Admin\Type\Material\MaterialChoiceTreeType;
use PrestaShopBundle\Form\Admin\Type\ShopChoiceTreeType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TextWithRecommendedLengthType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Defines Improve > Design > Pages cms page form
 */
class CmsPageType extends TranslatorAwareType
{
    public const TITLE_MAX_CHARS = 255;
    public const META_DESCRIPTION_MAX_CHARS = 512;
    public const META_KEYWORD_MAX_CHARS = 512;
    public const FRIENDLY_URL_MAX_CHARS = 128;
    public const RECOMMENDED_TITLE_LENGTH = 70;
    public const RECOMMENDED_DESCRIPTION_LENGTH = 160;

    /**
     * @var array
     */
    private $allCmsCategories;

    /**
     * @var bool
     */
    private $isMultiShopEnabled;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param array $allCmsCategories
     * @param bool $isMultiShopEnabled
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        array $allCmsCategories,
        $isMultiShopEnabled
    ) {
        parent::__construct($translator, $locales);

        $this->allCmsCategories = $allCmsCategories;
        $this->isMultiShopEnabled = $isMultiShopEnabled;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $invalidCharsText = sprintf('%s <>={}', $this->trans('Invalid characters:', 'Admin.Notifications.Info'));

        $builder
            ->add('page_category_id', MaterialChoiceTreeType::class, [
                'label' => $this->trans('Page category', 'Admin.Design.Feature'),
                'required' => false,
                'choices_tree' => $this->allCmsCategories,
                'choice_value' => 'id_cms_category',
            ])
            ->add('title', TranslatableType::class, [
                'label' => $this->trans('Title', 'Admin.Global'),
                'help' => sprintf(
                    '%s %s',
                    $this->trans('Used in the h1 page tag, and as the default title tag value.', 'Admin.Design.Help'),
                    $invalidCharsText
                ),
                'constraints' => [
                    new DefaultLanguage([
                        'message' => $this->trans(
                            'The field %field_name% is required at least in your default language.',
                            'Admin.Notifications.Error',
                            [
                                '%field_name%' => sprintf(
                                    '"%s"',
                                    $this->trans('Title', 'Admin.Global')
                                ),
                            ]
                        ),
                    ]),
                ],
                'options' => [
                    'attr' => [
                        'class' => 'js-copier-source-title',
                    ],
                    'constraints' => [
                        new TypedRegex([
                            'type' => 'generic_name',
                        ]),
                        new Length([
                            'max' => self::TITLE_MAX_CHARS,
                            'maxMessage' => $this->trans(
                                'This field cannot be longer than %limit% characters',
                                'Admin.Notifications.Error',
                                ['%limit%' => self::TITLE_MAX_CHARS]
                            ),
                        ]),
                    ],
                ],
            ])
            ->add('seo_preview', CustomContentType::class, [
                'label' => $this->trans('SEO preview', 'Admin.Global'),
                'help' => $this->trans('Here is a preview of how your page will appear in search engine results.', 'Admin.Global'),
                'template' => '@PrestaShop/Admin/Improve/Design/Cms/Blocks/seo_preview.html.twig',
                'data' => [
                    'cms_url' => $options['cms_preview_url'],
                ],
            ])
            ->add('meta_title', TranslatableType::class, [
                'label' => $this->trans('Meta title', 'Admin.Global'),
                'type' => TextWithRecommendedLengthType::class,
                'help' => sprintf('%s %s',
                    $this->trans('Used to override the title tag value. If left blank, the default title value is used.', 'Admin.Design.Help'),
                    $invalidCharsText
                ),
                'required' => false,
                'options' => [
                    'recommended_length' => self::RECOMMENDED_TITLE_LENGTH,
                    'attr' => [
                        'maxlength' => self::TITLE_MAX_CHARS,
                    ],
                    'constraints' => [
                        new TypedRegex([
                            'type' => 'generic_name',
                        ]),
                        new Length([
                            'max' => self::TITLE_MAX_CHARS,
                            'maxMessage' => $this->trans(
                                'This field cannot be longer than %limit% characters',
                                'Admin.Notifications.Error',
                                ['%limit%' => self::TITLE_MAX_CHARS]
                            ),
                        ]),
                    ],
                ],
            ])
            ->add('meta_description', TranslatableType::class, [
                'label' => $this->trans('Meta description', 'Admin.Global'),
                'type' => TextWithRecommendedLengthType::class,
                'help' => $invalidCharsText,
                'required' => false,
                'options' => [
                    'recommended_length' => self::RECOMMENDED_DESCRIPTION_LENGTH,
                    'attr' => [
                        'maxlength' => self::META_DESCRIPTION_MAX_CHARS,
                    ],
                    'constraints' => [
                        new TypedRegex([
                            'type' => 'generic_name',
                        ]),
                        new Length([
                            'max' => self::META_DESCRIPTION_MAX_CHARS,
                            'maxMessage' => $this->trans(
                                'This field cannot be longer than %limit% characters',
                                'Admin.Notifications.Error',
                                ['%limit%' => self::META_DESCRIPTION_MAX_CHARS]
                            ),
                        ]),
                    ],
                ],
            ])
            ->add('meta_keyword', TranslatableType::class, [
                'label' => $this->trans('Meta keywords', 'Admin.Global'),
                'help' => sprintf(
                    '%s %s',
                    $this->trans('To add tags, click in the field, write something, and then press the "Enter" key.', 'Admin.Shopparameters.Help'),
                    $invalidCharsText
                ),
                'type' => TextType::class,
                'required' => false,
                'options' => [
                    'attr' => [
                        'class' => 'js-taggable-field',
                        'placeholder' => $this->trans('Add tag', 'Admin.Actions'),
                    ],
                    'constraints' => [
                        new TypedRegex([
                            'type' => 'generic_name',
                        ]),
                        new Length([
                            'max' => self::META_KEYWORD_MAX_CHARS,
                            'maxMessage' => $this->trans(
                                'This field cannot be longer than %limit% characters',
                                'Admin.Notifications.Error',
                                ['%limit%' => self::META_KEYWORD_MAX_CHARS]
                            ),
                        ]),
                    ],
                ],
            ])
            ->add('friendly_url', TranslatableType::class, [
                'label' => $this->trans('Friendly URL', 'Admin.Global'),
                'help' => $this->trans('Only letters and the hyphen (-) character are allowed.', 'Admin.Design.Feature'),
                'constraints' => [
                    new DefaultLanguage([
                        'message' => $this->trans(
                            'The field %field_name% is required at least in your default language.',
                            'Admin.Notifications.Error',
                            [
                                '%field_name%' => sprintf(
                                    '"%s"',
                                    $this->trans('Friendly URL', 'Admin.Global')
                                ),
                            ]
                        ),
                    ]),
                ],
                'options' => [
                    'attr' => [
                        'class' => 'js-copier-destination-friendly-url',
                    ],
                    'constraints' => [
                        new IsUrlRewrite(),
                        new Length([
                            'max' => self::FRIENDLY_URL_MAX_CHARS,
                            'maxMessage' => $this->trans(
                                'This field cannot be longer than %limit% characters',
                                'Admin.Notifications.Error',
                                ['%limit%' => self::FRIENDLY_URL_MAX_CHARS]
                            ),
                        ]),
                    ],
                ],
            ])
            ->add('content', TranslatableType::class, [
                'label' => $this->trans('Page content', 'Admin.Design.Feature'),
                'type' => FormattedTextareaType::class,
                'required' => false,
                'options' => [
                    'constraints' => [
                        new CleanHtml([
                            'message' => $this->trans(
                                '%s is invalid.',
                                'Admin.Notifications.Error'
                            ),
                        ]),
                    ],
                ],
            ])
            ->add('is_indexed_for_search', SwitchType::class, [
                'label' => $this->trans('Indexation by search engines', 'Admin.Design.Feature'),
                'required' => false,
            ])
            ->add('is_displayed', SwitchType::class, [
                'label' => $this->trans('Displayed', 'Admin.Global'),
                'required' => false,
            ]);

        if ($this->isMultiShopEnabled) {
            $builder->add('shop_association', ShopChoiceTreeType::class, [
                'label' => $this->trans('Store association', 'Admin.Global'),
                'required' => false,
                'attr' => [
                    'class' => 'js-shop-assoc-tree',
                ],
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

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'cms_preview_url' => '',
            ])
            ->setAllowedTypes('cms_preview_url', 'string')
        ;
    }
}
