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
use PrestaShopBundle\Translation\TranslatorAwareTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Defines form for Improve > Design > Pages > Categories create/edit actions
 */
class CmsPageCategoryType extends AbstractType
{
    use TranslatorAwareTrait;

    /**
     * @var array
     */
    private $allCmsCategories;

    /**
     * @var bool
     */
    private $isShopFeatureEnabled;

    /**
     * @param array $allCmsCategories
     * @param bool $isShopFeatureEnabled
     */
    public function __construct(array $allCmsCategories, $isShopFeatureEnabled)
    {
        $this->allCmsCategories = $allCmsCategories;
        $this->isShopFeatureEnabled = $isShopFeatureEnabled;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TranslatableType::class, [
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
                            'maxMessage' => $this->translator->trans(
                                'This field cannot be longer than %limit% characters',
                                ['%limit%' => 64],
                                'Admin.Notifications.Error'
                            ),
                        ]),
                    ],
                ],
            ])
            ->add('is_displayed', SwitchType::class, [
                'required' => false,
            ])
            ->add('parent_category', MaterialChoiceTreeType::class, [
                'required' => false,
                'choices_tree' => $this->allCmsCategories,
                'choice_value' => 'id_cms_category',
            ])
            ->add('description', TranslatableType::class, [
                'required' => false,
                'type' => TextareaType::class,
                'options' => [
                    'constraints' => [
                        new CleanHtml(),
                    ],
                ],
            ])
            ->add('meta_title', TranslatableType::class, [
                'required' => false,
                'options' => [
                    'constraints' => [
                        new TypedRegex([
                            'type' => 'generic_name',
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
                        new TypedRegex([
                            'type' => 'generic_name',
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
            ->add('meta_keywords', TranslatableType::class, [
                'required' => false,
                'options' => [
                    'constraints' => [
                        new TypedRegex([
                            'type' => 'generic_name',
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
                    'attr' => [
                        'placeholder' => $this->trans('Add tag', [], 'Admin.Actions'),
                    ],
                ],
            ])
            ->add('friendly_url', TranslatableType::class, [
                'constraints' => [
                    new DefaultLanguage(),
                ],
                'options' => [
                    'constraints' => [
                        new IsUrlRewrite(),
                        new Length([
                            'max' => 64,
                            'maxMessage' => $this->translator->trans(
                                'This field cannot be longer than %limit% characters',
                                ['%limit%' => 64],
                                'Admin.Notifications.Error'
                            ),
                        ]),
                    ],
                ],
            ])
        ;

        if ($this->isShopFeatureEnabled) {
            $builder->add('shop_association', ShopChoiceTreeType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans(
                            'The %s field is required.',
                            [
                                sprintf('"%s"', $this->trans('Shop association', [], 'Admin.Global')),
                            ],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
            ]);
        }
    }
}
