<?php
/**
 * 2007-2018 PrestaShop.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Form\Admin\Product;

use PrestaShopBundle\Form\Admin\Type\TypeaheadProductCollectionType;
use PrestaShopBundle\Form\Admin\Type\TranslateType;
use PrestaShopBundle\Form\Admin\Type\CommonAbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type as FormType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use PrestaShop\PrestaShop\Core\Product\ProductInterface;

/**
 * This form class is responsible to generate the product SEO form.
 */
class ProductSeo extends CommonAbstractType
{
    private $translator;
    private $locales;
    private $router;

    /**
     * Constructor.
     *
     * @param object $translator
     * @param object $legacyContext
     */
    public function __construct($translator, $legacyContext, $router)
    {
        $this->translator = $translator;
        $this->context = $legacyContext;
        $this->locales = $legacyContext->getLanguages();
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     *
     * Builds form
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $remoteUrls = [
            ProductInterface::REDIRECT_TYPE_PRODUCT_MOVED_PERMANENTLY => $this->context->getAdminLink('', false) . 'ajax_products_list.php?forceJson=1&disableCombination=1&exclude_packs=0&excludeVirtuals=0&limit=20&q=%QUERY',
            ProductInterface::REDIRECT_TYPE_PRODUCT_FOUND => $this->context->getAdminLink('', false) . 'ajax_products_list.php?forceJson=1&disableCombination=1&exclude_packs=0&excludeVirtuals=0&limit=20&q=%QUERY',
            ProductInterface::REDIRECT_TYPE_CATEGORY_MOVED_PERMANENTLY => $this->router->generate('admin_get_ajax_categories') . '&query=%QUERY',
            ProductInterface::REDIRECT_TYPE_CATEGORY_FOUND => $this->router->generate('admin_get_ajax_categories') . '&query=%QUERY',
        ];

        $builder->add(
            'meta_title',
            TranslateType::class,
            [
                'type' => FormType\TextType::class,
                'options' => [
                    'attr' => [
                        'placeholder' => $this->translator->trans('To have a different title from the product name, enter it here.', [], 'Admin.Catalog.Help'),
                        'counter' => 70,
                        'counter_type' => 'recommended',
                    ],
                    'required' => false,
                ],
                'locales' => $this->locales,
                'hideTabs' => true,
                'label' => $this->translator->trans('Meta title', [], 'Admin.Catalog.Feature'),
                'label_attr' => [
                    'popover' => $this->translator->trans('Public title for the product\'s page, and for search engines. Leave blank to use the product name. The number of remaining characters is displayed to the left of the field.', [], 'Admin.Catalog.Help'),
                    'popover_placement' => 'right',
                    'class' => 'px-0',
                ],
                'required' => false,
            ]
        )
            ->add(
                'meta_description',
                TranslateType::class,
                [
                    'type' => FormType\TextareaType::class,
                    'options' => [
                        'attr' => [
                            'placeholder' => $this->translator->trans('To have a different description than your product summary in search results pages, write it here.', [], 'Admin.Catalog.Help'),
                            'counter' => 160,
                            'counter_type' => 'recommended',
                        ],
                        'required' => false,
                    ],
                    'locales' => $this->locales,
                    'hideTabs' => true,
                    'label' => $this->translator->trans('Meta description', [], 'Admin.Catalog.Feature'),
                    'label_attr' => [
                        'popover' => $this->translator->trans('This description will appear in search engines. You need a single sentence, shorter than 160 characters (including spaces)', [], 'Admin.Catalog.Help'),
                        'popover_placement' => 'right',
                        'class' => 'px-0',
                    ],
                    'required' => false,
                ]
            )
            ->add(
                'link_rewrite',
                TranslateType::class,
                [
                    'type' => FormType\TextType::class,
                    'options' => [],
                    'locales' => $this->locales,
                    'hideTabs' => true,
                    'label' => $this->translator->trans('Friendly URL', [], 'Admin.Catalog.Feature'),
                ]
            )
            ->add(
                'redirect_type',
                FormType\ChoiceType::class,
                [
                    'choices' => [
                        $this->translator->trans('Permanent redirection to a category (301)', [], 'Admin.Catalog.Feature') => ProductInterface::REDIRECT_TYPE_CATEGORY_MOVED_PERMANENTLY,
                        $this->translator->trans('Temporary redirection to a category (302)', [], 'Admin.Catalog.Feature') => ProductInterface::REDIRECT_TYPE_CATEGORY_FOUND,
                        $this->translator->trans('Permanent redirection to a product (301)', [], 'Admin.Catalog.Feature') => ProductInterface::REDIRECT_TYPE_PRODUCT_MOVED_PERMANENTLY,
                        $this->translator->trans('Temporary redirection to a product (302)', [], 'Admin.Catalog.Feature') => ProductInterface::REDIRECT_TYPE_PRODUCT_FOUND,
                        $this->translator->trans('No redirection (404)', [], 'Admin.Catalog.Feature') => ProductInterface::REDIRECT_TYPE_NOT_FOUND,
                    ],
                    'choice_attr' => function ($val, $key, $index) use ($remoteUrls) {
                        if (array_key_exists($index, $remoteUrls)) {
                            return ['data-remoteurl' => $remoteUrls[$index]];
                        }

                        return [];
                    },
                    'required' => true,
                    'label' => $this->translator->trans('Redirection when offline', [], 'Admin.Catalog.Feature'),
                    'attr' => [
                        'data-labelproduct' => $this->translator->trans('Target product', [], 'Admin.Catalog.Feature'),
                        'data-placeholderproduct' => $this->translator->trans('To which product the page should redirect?', [], 'Admin.Catalog.Help'),
                        'data-labelcategory' => $this->translator->trans('Target category', [], 'Admin.Catalog.Feature'),
                        'data-placeholdercategory' => $this->translator->trans('To which category the page should redirect?', [], 'Admin.Catalog.Help'),
                        'data-hintcategory' => $this->translator->trans('If no category is selected the Main Category is used', [], 'Admin.Catalog.Help'),
                    ],
                ]
            )
            ->add(
                'id_type_redirected',
                TypeaheadProductCollectionType::class,
                [
                    'remote_url' => $this->context->getAdminLink('', false) . 'ajax_products_list.php?forceJson=1&disableCombination=1&exclude_packs=0&excludeVirtuals=0&limit=20&q=%QUERY',
                    'mapping_value' => 'id',
                    'mapping_name' => 'name',
                    'mapping_type' => $options['mapping_type'],
                    'template_collection' => '<span class="label">%s</span><i class="material-icons delete">clear</i>',
                    'limit' => 1,
                    'required' => false,
                    'label' => $this->translator->trans('Target', [], 'Admin.Catalog.Feature'),
                ]
            );
    }

    /**
     * Returns the block prefix of this type.
     *
     * @return string The prefix name
     */
    public function getBlockPrefix()
    {
        return 'product_seo';
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'mapping_type' => 'product',
        ]);
    }
}
