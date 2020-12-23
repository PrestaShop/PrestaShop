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
use PrestaShopBundle\Form\Admin\Type\Material\MaterialChoiceTreeType;
use PrestaShopBundle\Form\Admin\Type\ShopChoiceTreeType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Defines form for Improve > Design > Pages > Categories create/edit actions
 */
class CmsPageCategoryType extends TranslatorAwareType
{
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
        $invalidCharactersForCatalogLabel = $this->trans('Invalid characters:', 'Admin.Global') .  '<>;=#{}';
        $invalidCharactersForNameLabel = $this->trans('Invalid characters:', 'Admin.Global') . '<>={}';
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
                            'max' => 64,
                            'maxMessage' => $this->trans(
                                'This field cannot be longer than %limit% characters',
                                'Admin.Notifications.Error',
                                ['%limit%' => 64]
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
                            'max' => 255,
                            'maxMessage' => $this->trans(
                                'This field cannot be longer than %limit% characters',
                                'Admin.Notifications.Error',
                                ['%limit%' => 255]
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
                            'max' => 512,
                            'maxMessage' => $this->trans(
                                'This field cannot be longer than %limit% characters',
                                'Admin.Notifications.Error',
                                ['%limit%' => 512]
                            ),
                        ]),
                    ],
                ],
            ])
            ->add('meta_keywords', TranslatableType::class, [
                'label' => $this->trans('Meta keywords', 'Admin.Global'),
                'help' => $invalidCharactersForNameLabel,
                'required' => false,
                'options' => [
                    'constraints' => [
                        new TypedRegex([
                            'type' => 'generic_name',
                        ]),
                        new Length([
                            'max' => 255,
                            'maxMessage' => $this->trans(
                                'This field cannot be longer than %limit% characters',
                                'Admin.Notifications.Error',
                                ['%limit%' => 255]
                            ),
                        ]),
                    ],
                    'attr' => [
                        'placeholder' => $this->trans('Add tag', 'Admin.Actions'),
                    ],
                ],
            ])
            ->add('friendly_url', TranslatableType::class, [
                'label' => $this->trans('Friendly URL', 'Admin.Global'),
                'help' => $this->trans('Only letters and the minus (-) character are allowed.', 'Admin.Catalog.Help'),
                'constraints' => [
                    new DefaultLanguage(),
                ],
                'options' => [
                    'constraints' => [
                        new IsUrlRewrite(),
                        new Length([
                            'max' => 64,
                            'maxMessage' => $this->trans(
                                'This field cannot be longer than %limit% characters',
                                'Admin.Notifications.Error',
                                ['%limit%' => 64]
                            ),
                        ]),
                    ],
                ],
            ])
        ;

        if ($this->isShopFeatureEnabled) {
            $builder->add('shop_association', ShopChoiceTreeType::class, [
                'label' => $this->trans('Shop association', 'Admin.Global'),
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans(
                            'The %s field is required.',
                            'Admin.Notifications.Error',
                            [
                                sprintf('"%s"', $this->trans('Shop association', 'Admin.Global')),
                            ]
                        ),
                    ]),
                ],
            ]);
        }
    }
}
