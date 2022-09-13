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

use PrestaShop\PrestaShop\Adapter\Shop\Url\ProductPreviewProvider;
use PrestaShop\PrestaShop\Adapter\Shop\Url\ProductProvider;
use PrestaShopBundle\Form\Admin\Type\IconButtonType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

class FooterType extends TranslatorAwareType
{
    /**
     * @var ProductProvider
     */
    private $productUrlProvider;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var ProductPreviewProvider
     */
    protected $productPreviewUrlProvider;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param ProductProvider $productUrlProvider
     * @param ProductPreviewProvider $productPreviewUrlProvider
     * @param RouterInterface $router
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        ProductProvider $productUrlProvider,
        ProductPreviewProvider $productPreviewUrlProvider,
        RouterInterface $router
    ) {
        parent::__construct($translator, $locales);
        $this->productUrlProvider = $productUrlProvider;
        $this->productPreviewUrlProvider = $productPreviewUrlProvider;
        $this->router = $router;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $productId = $options['product_id'];

        $deleteUrl = $this->router->generate('admin_products_v2_delete', [
            'productId' => $productId,
        ]);
        $duplicateUrl = $this->router->generate('admin_products_v2_duplicate', [
            'productId' => $productId,
        ]);
        $editUrl = $this->router->generate('admin_products_v2_edit', [
            'productId' => $productId,
        ]);
        $productPreviewUrl = $this->productPreviewUrlProvider->getUrl($productId, $options['active']);
        // We use a placeholder {friendly-url} as the rewrite part so that it can be replaced dynamically by js
        $seoUrl = $this->productUrlProvider->getUrl($productId, '{friendly-url}');

        $builder
            ->add('catalog', IconButtonType::class, [
                'label' => $this->trans('Go to catalog', 'Admin.Catalog.Feature'),
                'type' => 'link',
                'icon' => 'arrow_back_ios',
                'attr' => [
                    'class' => 'btn-outline-secondary border-white go-to-catalog-button',
                    'href' => $this->router->generate('admin_products_v2_index', ['offset' => 'last', 'limit' => 'last']),
                ],
            ])
            ->add('duplicate_product', IconButtonType::class, [
                'label' => $this->trans('Duplicate', 'Admin.Actions'),
                'type' => 'link',
                'attr' => [
                    'class' => 'btn-outline-secondary duplicate-product-button',
                    'data-modal-title' => $this->trans('Duplicate product?', 'Admin.Catalog.Notification'),
                    'data-modal-apply' => $this->trans('Duplicate', 'Admin.Actions'),
                    'data-modal-cancel' => $this->trans('Cancel', 'Admin.Actions'),
                    'data-confirm-button-class' => 'btn-primary',
                    'data-button-url' => $duplicateUrl,
                ],
            ])
            ->add('delete', IconButtonType::class, [
                'label' => $this->trans('Delete', 'Admin.Actions'),
                'attr' => [
                    'class' => 'tooltip-link delete-product-button btn-outline-secondary',
                    'data-modal-title' => $this->trans('Permanently delete this product?', 'Admin.Catalog.Notification'),
                    'data-modal-apply' => $this->trans('Delete', 'Admin.Actions'),
                    'data-modal-cancel' => $this->trans('Cancel', 'Admin.Actions'),
                    'data-confirm-button-class' => 'btn-danger',
                    'data-button-url' => $deleteUrl,
                ],
            ])
            ->add('new_product', IconButtonType::class, [
                'label' => $this->trans('New product', 'Admin.Catalog.Feature'),
                'type' => 'link',
                'attr' => [
                    'class' => 'btn-outline-secondary new-product-button',
                    'href' => $this->router->generate('admin_products_v2_create'),
                ],
            ])
            ->add('cancel', IconButtonType::class, [
                'label' => $this->trans('Cancel', 'Admin.Actions'),
                'type' => 'link',
                'attr' => [
                    'href' => $editUrl,
                    'class' => 'btn-secondary cancel-button',
                    'disabled' => true,
                ],
            ])
            // These two inputs are displayed separately
            ->add('preview', IconButtonType::class, [
                'label' => $this->trans('Preview', 'Admin.Actions'),
                'type' => 'link',
                'attr' => [
                    'target' => '_blank',
                    'href' => $productPreviewUrl,
                    'class' => 'btn-outline-secondary preview-url-button',
                    'data-seo-url' => $seoUrl,
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => $options['active'] ? $this->trans('Save and publish', 'Admin.Actions') : $this->trans('Save', 'Admin.Actions'),
                'attr' => [
                    'data-toggle' => 'pstooltip',
                    'accesskey' => 's',
                    'disabled' => true,
                    'title' => $this->trans('Save the product and stay on the current page: ALT+SHIFT+S', 'Admin.Catalog.Help'),
                    'class' => 'btn-primary product-form-save-button',
                ],
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
                'active' => false,
                'required' => false,
                'label' => false,
                'attr' => [
                    'class' => 'footer-buttons-container',
                ],
                'row_attr' => [
                    'class' => 'product-footer-left',
                ],
            ])
            ->setRequired([
                'product_id',
            ])
            ->setAllowedTypes('product_id', 'int')
            ->setAllowedTypes('active', ['bool'])
        ;
    }
}
