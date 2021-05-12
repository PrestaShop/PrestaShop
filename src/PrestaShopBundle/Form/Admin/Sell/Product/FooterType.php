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

use PrestaShop\PrestaShop\Adapter\Shop\Url\ProductProvider;
use PrestaShopBundle\Form\Admin\Type\IconButtonType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
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
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param ProductProvider $productUrlProvider
     * @param RouterInterface $router
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        ProductProvider $productUrlProvider,
        RouterInterface $router
    ) {
        parent::__construct($translator, $locales);
        $this->productUrlProvider = $productUrlProvider;
        $this->router = $router;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $productId = !empty($options['product_id']) ? (int) $options['product_id'] : null;
        $deleteUrl = $productId ? $this->router->generate('admin_product_unit_action', [
            'action' => 'delete',
            'id' => $productId,
        ]) : null;
        $seoUrl = $productId ? $this->productUrlProvider->getUrl($productId, '{friendly-url}') : null;
        $standardPageUrl = $productId ? $this->router->generate('admin_product_form', ['id' => $productId]) : $this->router->generate('admin_product_new');
        $duplicateUrl = $productId ? $this->router->generate('admin_product_unit_action', [
            'action' => 'duplicate',
            'id' => $productId,
        ]) : null;

        $builder
            ->add('catalog', IconButtonType::class, [
                'label' => $this->trans('Go to catalog', 'Admin.Catalog.Feature'),
                'type' => 'link',
                'icon' => 'arrow_back_ios',
                'attr' => [
                    'class' => 'btn-outline-secondary',
                    'href' => $this->router->generate('admin_product_catalog', ['offset' => 'last', 'limit' => 'last']),
                ],
            ])
            ->add('preview', IconButtonType::class, [
                'label' => $this->trans('Preview', 'Admin.Actions'),
                'icon' => 'remove_red_eye',
                'attr' => [
                    'class' => 'btn-outline-secondary preview-url-button',
                    'data-seo-url' => $seoUrl,
                    'disabled' => empty($productId),
                ],
            ])
            ->add('standard_page', IconButtonType::class, [
                'label' => $this->trans('Back to standard page', 'Admin.Catalog.Feature'),
                'type' => 'link',
                'attr' => [
                    'class' => 'btn-outline-secondary',
                    'href' => $standardPageUrl,
                ],
            ])
            ->add('delete', IconButtonType::class, [
                'icon' => 'delete',
                'label' => $this->trans('Delete', 'Admin.Actions'),
                'attr' => [
                    'class' => 'tooltip-link delete-product-button btn-outline-secondary',
                    'data-modal-title' => $this->trans('Permanently delete this product.', 'Admin.Catalog.Help'),
                    'data-modal-message' => $this->trans('Are you sure you want to delete this item?', 'Admin.Notifications.Warning'),
                    'data-modal-apply' => $this->trans('Delete', 'Admin.Actions'),
                    'data-modal-cancel' => $this->trans('Cancel', 'Admin.Actions'),
                    'data-remove-url' => $deleteUrl,
                    'data-toggle' => 'pstooltip',
                    'data-placement' => 'left',
                    'title' => $this->trans('Permanently delete this product.', 'Admin.Catalog.Help'),
                    'disabled' => empty($productId),
                ],
            ])
            ->add('active', SwitchType::class, [
                'label' => false,
                'choices' => [
                    $this->trans('Offline', 'Admin.Global') => false,
                    $this->trans('Online', 'Admin.Global') => true,
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => $this->trans('Save', 'Admin.Actions'),
                'attr' => [
                    'data-toggle' => 'pstooltip',
                    'disabled' => true,
                    'title' => $this->trans('Save the product and stay on the current page: ALT+SHIFT+S', 'Admin.Catalog.Help'),
                    'class' => 'btn-primary product-form-save-button',
                ],
            ])
            ->add('duplicate_product', IconButtonType::class, [
                'label' => $this->trans('Duplicate', 'Admin.Actions'),
                'type' => 'link',
                'icon' => 'content_copy',
                'attr' => [
                    'class' => 'btn-outline-secondary',
                    'href' => $duplicateUrl,
                    'disabled' => empty($productId),
                ],
            ])
            ->add('new_product', IconButtonType::class, [
                'label' => $this->trans('New product on experimental page', 'Admin.Catalog.Feature'),
                'type' => 'link',
                'icon' => 'add_circle_outline',
                'attr' => [
                    'class' => 'btn-outline-secondary',
                    'href' => $this->router->generate('admin_products_v2_create'),
                    'disabled' => empty($productId),
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
                'product_id' => null,
                'required' => false,
                'label' => false,
                'attr' => [
                    'class' => 'product-footer-right',
                ],
            ])
            ->setAllowedTypes('product_id', ['null', 'int'])
        ;
    }
}
