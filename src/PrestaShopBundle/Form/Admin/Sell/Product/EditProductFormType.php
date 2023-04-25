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

use PrestaShopBundle\Form\Admin\Sell\Product\Combination\CombinationsType;
use PrestaShopBundle\Form\Admin\Sell\Product\Description\DescriptionType;
use PrestaShopBundle\Form\Admin\Sell\Product\Details\DetailsType;
use PrestaShopBundle\Form\Admin\Sell\Product\Options\OptionsType;
use PrestaShopBundle\Form\Admin\Sell\Product\Pricing\PricingType;
use PrestaShopBundle\Form\Admin\Sell\Product\SEO\SEOType;
use PrestaShopBundle\Form\Admin\Sell\Product\Shipping\ShippingType;
use PrestaShopBundle\Form\Admin\Sell\Product\Stock\StockType;
use PrestaShopBundle\Form\Admin\Type\NavigationTabType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use PrestaShopBundle\Form\Toolbar\ToolbarButtonsProviderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * This is the parent product form type
 */
class EditProductFormType extends TranslatorAwareType
{
    /**
     * @var EventSubscriberInterface
     */
    private $productTypeListener;

    /**
     * @var ToolbarButtonsProviderInterface
     */
    private $toolbarButtonsProvider;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param EventSubscriberInterface $productTypeListener
     * @param ToolbarButtonsProviderInterface $toolbarButtonsProvider
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        EventSubscriberInterface $productTypeListener,
        ToolbarButtonsProviderInterface $toolbarButtonsProvider
    ) {
        parent::__construct($translator, $locales);
        $this->productTypeListener = $productTypeListener;
        $this->toolbarButtonsProvider = $toolbarButtonsProvider;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $productId = $options['product_id'];
        $shopId = $options['shop_id'];

        $builder
            ->add('header', HeaderType::class, [
                'active' => $options['active'],
                'product_id' => $productId,
            ])
            ->add('description', DescriptionType::class, [
                'product_id' => $productId,
                'shop_id' => $shopId,
            ])
            ->add('details', DetailsType::class)
            ->add('combinations', CombinationsType::class, [
                'product_id' => $productId,
            ])
            ->add('stock', StockType::class, [
                'product_id' => $productId,
                'product_type' => $options['product_type'],
                'virtual_product_file_id' => $options['virtual_product_file_id'],
            ])
            ->add('shipping', ShippingType::class)
            ->add('pricing', PricingType::class, [
                'tax_rules_group_id' => $options['tax_rules_group_id'],
            ])
            ->add('seo', SEOType::class, [
                'product_id' => $productId,
            ])
            ->add('options', OptionsType::class)
            ->add('extra_modules', ExtraModulesType::class, [
                'product_id' => $productId,
            ])
            ->add('footer', FooterType::class, [
                'product_id' => $productId,
                'active' => $options['active'],
            ])
        ;

        /*
         * This listener adapts the content of the form based on the Product type, it can remove add or transforms some
         * of the internal fields @see ProductTypeListener
         */
        $builder->addEventSubscriber($this->productTypeListener);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $formVars = [
            'product_type' => $options['product_type'],
            'product_id' => $options['product_id'],
            'shop_id' => $options['shop_id'],
        ];

        $view->vars = array_replace($view->vars, $formVars);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        // We must allow extra fields because when we switch product type some former fields may be present in request
        $resolver
            ->setDefaults([
                'virtual_product_file_id' => null,
                'active' => false,
                'allow_extra_fields' => true,
                'form_theme' => '@PrestaShop/Admin/Sell/Catalog/Product/FormTheme/product.html.twig',
                'use_default_themes' => false,
                'toolbar_buttons' => [],
                'toolbar_options' => [
                    'use_inline_labels' => false,
                ],
            ])
            ->setRequired([
                'product_id',
                'shop_id',
                'product_type',
                'tax_rules_group_id',
            ])
            ->setAllowedTypes('product_id', 'int')
            ->setAllowedTypes('shop_id', 'int')
            ->setAllowedTypes('product_type', 'string')
            ->setAllowedTypes('virtual_product_file_id', ['null', 'int'])
            ->setAllowedTypes('active', ['bool'])
            ->setNormalizer('toolbar_buttons', function (Options $options, $toolbarButtons) {
                return array_merge(
                    $this->toolbarButtonsProvider->getToolbarButtonsOptions(['productId' => $options->offsetGet('product_id')]),
                    $toolbarButtons
                );
            })
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function getParent()
    {
        return NavigationTabType::class;
    }

    /**
     * {@inheritDoc}
     */
    public function getBlockPrefix()
    {
        return 'product';
    }
}
