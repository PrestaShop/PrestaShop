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

use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
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
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\Length;

class DescriptionType extends TranslatorAwareType
{
    /**
     * @var LegacyContext
     */
    private $legacyContext;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param LegacyContext $legacyContext
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        LegacyContext $legacyContext
    ) {
        parent::__construct($translator, $locales);
        $this->legacyContext = $legacyContext;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $formIsUsedToEditAProduct = !empty($options['product_id']);
        if ($formIsUsedToEditAProduct) {
            $productId = (int) $options['product_id'];
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
            ->add('categories', CategoriesType::class)
            ->add('manufacturer', ManufacturerType::class)
            ->add('tags', TranslatableType::class, [
                'required' => false,
                'label' => $this->trans('Tags', 'Admin.Catalog.Feature'),
                'options' => [
                    'constraints' => [
                        new TypedRegex(TypedRegex::TYPE_GENERIC_NAME),
                    ],
                    'attr' => [
                        'class' => 'js-taggable-field',
                        'placeholder' => $this->trans('Use a comma to create separate tags. E.g.: dress, cotton, party dresses.', 'Admin.Catalog.Help'),
                    ],
                    'required' => false,
                ],
                'alert_title' => $this->trans('Tags are meant to help your customers find your products via the search bar.', 'Admin.Catalog.Help'),
                'alert_message' => [
                    $this->trans('Choose terms and keywords that your customers will use to search for this product and make sure you are consistent with the tags you may have already used.', 'Admin.Catalog.Help'),
                    $this->trans('You can manage tag aliases in the [1]Search section[/1]. If you add new tags, you have to rebuild the index.', 'Admin.Catalog.Help', [
                        '[1]' => sprintf(
                            '<a target="_blank" href="%s">',
                            $this->legacyContext->getAdminLink('AdminSearchConf')
                        ),
                        '[/1]' => '</a>',
                    ]),
                ],
            ])
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
