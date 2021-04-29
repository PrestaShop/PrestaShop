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

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Product;

use PrestaShop\PrestaShop\Core\Domain\Product\ProductSettings;
use PrestaShopBundle\Form\Admin\Type\TextWithLengthCounterType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;

class SEOType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('meta_title', TranslatableType::class, [
                'label' => $this->trans('Meta title', 'Admin.Catalog.Feature'),
                'label_help_box' => $this->trans('Public title for the product page and for search engines. Leave blank to use the product name. The number of remaining characters is displayed to the left of the field.', 'Admin.Catalog.Help'),
                'required' => false,
                'type' => TextWithLengthCounterType::class,
                'help' => $this->trans(
                    'Public title for the product\'s page, and for search engines. Leave blank to use the product name. The number of remaining characters is displayed to the left of the field.',
                    'Admin.Catalog.Help'
                ),
                'options' => [
                    'input' => 'text',
                    'input_attr' => [
                        'class' => 'serp-watched-title',
                    ],
                    'max_length' => ProductSettings::MAX_META_TITLE_LENGTH,
                    'position' => 'after',
                    'constraints' => [
                        new Length([
                            'max' => ProductSettings::MAX_META_TITLE_LENGTH,
                            'maxMessage' => $this->trans(
                                'This field cannot be longer than %limit% characters.',
                                'Admin.Notifications.Error',
                                ['%limit%' => ProductSettings::MAX_META_TITLE_LENGTH]
                            ),
                        ]),
                    ],
                ],
            ])
            ->add('meta_description', TranslatableType::class, [
                'label' => $this->trans('Meta description', 'Admin.Catalog.Feature'),
                'label_help_box' => $this->trans('This description will appear in search engines. You need a single sentence, shorter than 160 characters (including spaces)', 'Admin.Catalog.Help'),
                'required' => false,
                'type' => TextWithLengthCounterType::class,
                'help' => $this->trans(
                    'This description will appear in search engines. It should be a single sentence, shorter than 160 characters (including spaces).',
                    'Admin.Catalog.Help'
                ),
                'options' => [
                    'input' => 'textarea',
                    'input_attr' => [
                        'class' => 'serp-watched-description',
                    ],
                    'max_length' => ProductSettings::MAX_META_DESCRIPTION_LENGTH,
                    'position' => 'after',
                    'constraints' => [
                        new Length([
                            'max' => ProductSettings::MAX_META_DESCRIPTION_LENGTH,
                            'maxMessage' => $this->trans(
                                'This field cannot be longer than %limit% characters.',
                                'Admin.Notifications.Error',
                                ['%limit%' => ProductSettings::MAX_META_DESCRIPTION_LENGTH]
                            ),
                        ]),
                    ],
                ],
            ])
            ->add('link_rewrite', TranslatableType::class, [
                'label' => $this->trans('Friendly URL', 'Admin.Catalog.Feature'),
                'label_help_box' => $this->trans('This is the human-readable URL, as generated from the product\'s name. You can change it if you want.', 'Admin.Catalog.Help'),
                'required' => false,
                'type' => TextType::class,
                'help' => $this->trans(
                    'This is the human-readable URL, as generated from the product\'s name. You can change it if you want.',
                    'Admin.Catalog.Help'
                ),
                'options' => [
                    'attr' => [
                        'class' => 'serp-watched-url',
                    ],
                ],
            ])
        ;
    }
}
