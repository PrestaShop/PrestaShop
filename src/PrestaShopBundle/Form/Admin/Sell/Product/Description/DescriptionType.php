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
use PrestaShopBundle\Form\Admin\Type\ProductSearchType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Contracts\Translation\TranslatorInterface;

class DescriptionType extends TranslatorAwareType
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var string
     */
    private $employeeIsoCode;

    /**
     * @var int
     */
    private $shortDescriptionMaxLength;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param RouterInterface $router
     * @param string $employeeIsoCode
     * @param int $shortDescriptionMaxLength
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        RouterInterface $router,
        string $employeeIsoCode,
        int $shortDescriptionMaxLength
    ) {
        parent::__construct($translator, $locales);
        $this->router = $router;
        $this->employeeIsoCode = $employeeIsoCode;
        $this->shortDescriptionMaxLength = $shortDescriptionMaxLength;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $productId = $options['product_id'];
        $shopId = $options['shop_id'];

        if ($this->shortDescriptionMaxLength > 0) {
            $shortDescriptionLimit = $this->shortDescriptionMaxLength;
        } else {
            $shortDescriptionLimit = ProductSettings::MAX_DESCRIPTION_SHORT_LENGTH;
        }

        $builder
            ->add('images', ImageDropzoneType::class, [
                'product_id' => $productId,
                'shop_id' => $shopId,
                'update_form_type' => ProductImageType::class,
            ])
            ->add('description_short', TranslatableType::class, [
                'required' => false,
                'label' => $this->trans('Summary', 'Admin.Global'),
                'type' => FormattedTextareaType::class,
                'options' => [
                    'limit' => $shortDescriptionLimit,
                    'attr' => [
                        'class' => 'serp-default-description',
                    ],
                ],
                'label_tag_name' => 'h3',
                'modify_all_shops' => true,
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
                'label_tag_name' => 'h3',
                'modify_all_shops' => true,
            ])
            ->add('categories', CategoriesType::class, [
                'label' => $this->trans('Categories', 'Admin.Global'),
                'label_tag_name' => 'h3',
                'product_id' => $productId,
            ])
            ->add('manufacturer', ManufacturerType::class)
            ->add('related_products', ProductSearchType::class, [
                'include_combinations' => false,
                'label' => $this->trans('Related products', 'Admin.Catalog.Feature'),
                'label_tag_name' => 'h3',
                'entry_options' => [
                    'block_prefix' => 'related_product',
                ],
                'remote_url' => $this->router->generate('admin_products_search_products_for_association', [
                    'languageCode' => $this->employeeIsoCode,
                    'query' => '__QUERY__',
                ]),
                'min_length' => 3,
                'limit' => 0,
                'filtered_identities' => $productId > 0 ? [$productId] : [],
            ])
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver
            ->setDefaults([
                'required' => false,
                'label' => $this->trans('Description', 'Admin.Catalog.Feature'),
            ])
            ->setRequired([
                'product_id',
                'shop_id',
            ])
            ->setAllowedTypes('product_id', 'int')
            ->setAllowedTypes('shop_id', 'int')
        ;
    }
}
