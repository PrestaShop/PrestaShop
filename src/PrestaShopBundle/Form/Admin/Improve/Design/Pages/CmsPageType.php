<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Form\Admin\Improve\Design\Pages;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\CleanHtml;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DefaultLanguage;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\IsUrlRewrite;
use PrestaShopBundle\Form\Admin\Type\Material\MaterialChoiceTreeType;
use PrestaShopBundle\Form\Admin\Type\ShopChoiceTreeType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

/**
 * Defines Improve > Design > Pages cms page form
 */
class CmsPageType extends AbstractType
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

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
     * @param array $allCmsCategories
     * @param $isMultiShopEnabled
     */
    public function __construct(TranslatorInterface $translator, array $allCmsCategories, $isMultiShopEnabled)
    {
        $this->translator = $translator;
        $this->allCmsCategories = $allCmsCategories;
        $this->isMultiShopEnabled = $isMultiShopEnabled;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('page_category', MaterialChoiceTreeType::class, [
                'required' => false,
                'choices_tree' => $this->allCmsCategories,
                'choice_value' => 'id_cms_category',
            ])
            ->add('title', TranslatableType::class, [
                'error_bubbling' => false,
                'constraints' => [
                    new DefaultLanguage([
                        'message' => $this->translator->trans(
                            'The field %field_name% is required at least in your default language.',
                            [
                                '%field_name%' => sprintf(
                                    '"%s"',
                                    $this->translator->trans('Title', [], 'Admin.Global')
                                ),
                            ],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
                'options' => [
                    'constraints' => [
                        //@todo: typedRegexConstraint generic_name PR #12735
                        new Regex([
                            'pattern' => '/^[^<>={}]*$/u',
                            'message' => $this->translator->trans(
                                '%s is invalid.',
                                [],
                                'Admin.Notifications.Error'
                            ),
                        ]),
                        new Length([
                            'max' => 255,
                            'maxMessage' => $this->translator->trans(
                                'This field cannot be longer than %limit% characters',
                                ['%limit%' => 255],
                                'Admin.Notifications.Error'
                            ),
                        ]),
                    ],
                ],
            ])
            ->add('meta_title', TranslatableType::class, [
                'required' => false,
                'options' => [
                    'constraints' => [
                        //@todo: typedRegexConstraint generic_name PR #12735
                        new Regex([
                            'pattern' => '/^[^<>={}]*$/u',
                            'message' => $this->translator->trans(
                                '%s is invalid.',
                                [],
                                'Admin.Notifications.Error'
                            ),
                        ]),
                        new Length([
                            'max' => 255,
                            'maxMessage' => $this->translator->trans(
                                'This field cannot be longer than %limit% characters',
                                ['%limit%' => 255],
                                'Admin.Notifications.Error'
                            ),
                        ]),
                    ],
                ],
            ])
            ->add('meta_description', TranslatableType::class, [
                'required' => false,
                'options' => [
                    'constraints' => [
                        //@todo: typedRegexConstraint generic_name PR #12735
                        new Regex([
                            'pattern' => '/^[^<>={}]*$/u',
                            'message' => $this->translator->trans(
                                '%s is invalid.',
                                [],
                                'Admin.Notifications.Error'
                            ),
                        ]),
                    ],
                ],
            ])
            ->add('meta_keyword', TranslatableType::class, [
                'type' => TextType::class,
                'required' => false,
                'options' => [
                    'attr' => [
                        'class' => 'js-taggable-field',
                        'placeholder' => $this->translator->trans('Add tag', [], 'Admin.Actions'),
                    ],
                    'constraints' => [
                        //@todo: typedRegexConstraint generic_name PR #12735
                        new Regex([
                            'pattern' => '/^[^<>={}]*$/u',
                            'message' => $this->translator->trans(
                                '%s is invalid.',
                                [],
                                'Admin.Notifications.Error'
                            ),
                        ]),
                        new Length([
                            'max' => 512,
                            'maxMessage' => $this->translator->trans(
                                'This field cannot be longer than %limit% characters',
                                ['%limit%' => 512],
                                'Admin.Notifications.Error'
                            ),
                        ]),
                    ],
                ],
            ])
            ->add('friendly_url', TranslatableType::class, [
                'error_bubbling' => false,
                'constraints' => [
                    new DefaultLanguage([
                        'message' => $this->translator->trans(
                            'The field %field_name% is required at least in your default language.',
                            [
                                '%field_name%' => sprintf(
                                    '"%s"',
                                    $this->translator->trans('Friendly URL', [], 'Admin.Global')
                                ),
                            ],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
                'options' => [
                    'constraints' => [
                        new IsUrlRewrite(),
                        new Length([
                            'max' => 128,
                            'maxMessage' => $this->translator->trans(
                                'This field cannot be longer than %limit% characters',
                                ['%limit%' => 128],
                                'Admin.Notifications.Error'
                            ),
                        ]),
                    ],
                ],
            ])
            ->add('content', TranslatableType::class, [
                'type' => TextareaType::class,
                'required' => false,
                'options' => [
                    'constraints' => [
                        new CleanHtml([
                            'message' => $this->translator->trans(
                                '%s is invalid.',
                                [],
                                'Admin.Notifications.Error'
                            ),
                        ]),
                        new Length([
                            //@todo: according to legacy. Is there a reason for this???
                            'max' => 3999999999999,
                            'maxMessage' => $this->translator->trans(
                                'This field cannot be longer than %limit% characters',
                                ['%limit%' => 3999999999999],
                                'Admin.Notifications.Error'
                            ),
                        ]),
                    ],
                ],
            ])
            ->add('is_indexed_for_search', SwitchType::class, [
                'required' => false,
            ])
            ->add('is_displayed', SwitchType::class, [
                'required' => false,
            ])
        ;

        if ($this->isMultiShopEnabled) {
            $builder->add('shop_association', ShopChoiceTreeType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans(
                            'The %s field is required.',
                            [
                                sprintf('"%s"', $this->translator->trans('Shop association', [], 'Admin.Global')),
                            ],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
            ]);
        }
    }
}
