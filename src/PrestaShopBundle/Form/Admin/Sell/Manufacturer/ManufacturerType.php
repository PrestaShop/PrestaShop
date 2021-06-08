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

namespace PrestaShopBundle\Form\Admin\Sell\Manufacturer;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\CleanHtml;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShopBundle\Form\Admin\Type\FormattedTextareaType;
use PrestaShopBundle\Form\Admin\Type\ShopChoiceTreeType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Defines form for manufacturer create/edit actions (Sell > Catalog > Brands & Suppliers)
 */
class ManufacturerType extends TranslatorAwareType
{
    /**
     * @var bool
     */
    private $isMultistoreEnabled;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param bool $isMultistoreEnabled
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        $isMultistoreEnabled
    ) {
        parent::__construct($translator, $locales);

        $this->isMultistoreEnabled = $isMultistoreEnabled;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $invalidCharactersForCatalogLabel = $this->trans('Invalid characters:', 'Admin.Global') . '<>;=#{}';
        $invalidCharactersForNameLabel = $this->trans('Invalid characters:', 'Admin.Global') . '<>={}';

        $builder
            ->add('name', TextType::class, [
                'label' => $this->trans('Name', 'Admin.Global'),
                'help' => $invalidCharactersForCatalogLabel,
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans(
                            'This field cannot be empty.', 'Admin.Notifications.Error'
                        ),
                    ]),
                    new Length([
                        'max' => 64,
                        'maxMessage' => $this->trans(
                            'This field cannot be longer than %limit% characters',
                            'Admin.Notifications.Error',
                            ['%limit%' => 64]
                        ),
                    ]),
                    new TypedRegex([
                        'type' => 'catalog_name',
                    ]),
                ],
            ])
            ->add('short_description', TranslatableType::class, [
                'label' => $this->trans('Short description', 'Admin.Catalog.Feature'),
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
            ->add('description', TranslatableType::class, [
                'label' => $this->trans('Description', 'Admin.Global'),
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
            ->add('logo', FileType::class, [
                'label' => $this->trans('Logo', 'Admin.Global'),
                'help' => $this->trans('Upload a brand logo from your computer.', 'Admin.Catalog.Help'),
                'required' => false,
            ])
            ->add('meta_title', TranslatableType::class, [
                'label' => $this->trans('Meta title', 'Admin.Catalog.Feature'),
                'help' => $invalidCharactersForNameLabel,
                'type' => TextType::class,
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
                ],
            ])
            ->add('meta_description', TranslatableType::class, [
                'label' => $this->trans('Meta description', 'Admin.Catalog.Feature'),
                'help' => $invalidCharactersForNameLabel,
                'type' => TextareaType::class,
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
            ->add('meta_keyword', TranslatableType::class, [
                'label' => $this->trans('Meta keywords', 'Admin.Global'),
                'help' => $this->trans('To add tags, click in the field, write something, and then press the "Enter" key.', 'Admin.Shopparameters.Help')
                 . '<br>' . $invalidCharactersForNameLabel,
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
                    ],
                ],
            ])
            ->add('is_enabled', SwitchType::class, [
                'label' => $this->trans('Enabled', 'Admin.Global'),
                'required' => false,
            ]);

        if ($this->isMultistoreEnabled) {
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
}
