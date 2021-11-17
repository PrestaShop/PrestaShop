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

namespace PrestaShopBundle\Form\Admin\Sell\Product\Description;

use PrestaShop\PrestaShop\Core\Domain\Product\ProductSettings;
use PrestaShopBundle\Form\Admin\Sell\Product\Category\CategoriesType;
use PrestaShopBundle\Form\Admin\Sell\Product\Image\ImageDropzoneType;
use PrestaShopBundle\Form\Admin\Sell\Product\Image\ProductImageType;
use PrestaShopBundle\Form\Admin\Type\FormattedTextareaType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use PrestaShopBundle\Form\Admin\Type\UnavailableType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class DescriptionType extends TranslatorAwareType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $productId = (int) $options['product_id'];

        if ($productId) {
            $builder
                ->add('images', ImageDropzoneType::class, [
                    'product_id' => $productId,
                    'update_form_type' => ProductImageType::class,
                ])
            ;
        }

        $builder
            ->add('description_short', TranslatableType::class, [
                'required' => false,
                'label' => $this->trans('Summary', 'Admin.Global'),
                'type' => FormattedTextareaType::class,
                'options' => [
                    'limit' => ProductSettings::MAX_DESCRIPTION_SHORT_LENGTH,
                    'attr' => [
                        'class' => 'serp-default-description',
                    ],
                    'constraints' => [
                        new Length([
                            'max' => ProductSettings::MAX_DESCRIPTION_SHORT_LENGTH,
                            'maxMessage' => $this->trans(
                                'This field cannot be longer than %limit% characters.',
                                'Admin.Notifications.Error',
                                [
                                    '%limit%' => ProductSettings::MAX_DESCRIPTION_SHORT_LENGTH,
                                ]
                            ),
                        ]),
                    ],
                ],
                'label_tag_name' => 'h2',
            ])
            ->add('description', TranslatableType::class, [
                'required' => false,
                'label' => $this->trans('Description', 'Admin.Global'),
                'type' => FormattedTextareaType::class,
                'options' => [
                    'limit' => ProductSettings::MAX_DESCRIPTION_LENGTH,
                    'constraints' => [
                        new Length([
                            'max' => ProductSettings::MAX_DESCRIPTION_LENGTH,
                            'maxMessage' => $this->trans(
                                'This field cannot be longer than %limit% characters.',
                                'Admin.Notifications.Error',
                                [
                                    '%limit%' => ProductSettings::MAX_DESCRIPTION_LENGTH,
                                ]
                            ),
                        ]),
                    ],
                ],
                'label_tag_name' => 'h2',
            ])
            ->add('categories', CategoriesType::class, [
                'label' => $this->trans('Categories', 'Admin.Global'),
                'label_tag_name' => 'h2',
                'product_id' => $productId,
            ])
            ->add('manufacturer', ManufacturerType::class)
            ->add('related_products', UnavailableType::class, [
                'label' => $this->trans('Related products', 'Admin.Catalog.Feature'),
                'label_tag_name' => 'h2',
            ])
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver
            ->setDefaults([
                'product_id' => null,
                'required' => false,
                'label' => false,
            ])
            ->setAllowedTypes('product_id', ['null', 'int'])
        ;
    }
}
