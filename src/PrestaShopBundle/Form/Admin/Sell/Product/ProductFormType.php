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

use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use PrestaShopBundle\Form\Admin\Sell\Product\Description\DescriptionType;
use PrestaShopBundle\Form\Admin\Sell\Product\Options\OptionsType;
use PrestaShopBundle\Form\Admin\Sell\Product\Pricing\PricingType;
use PrestaShopBundle\Form\Admin\Sell\Product\SEO\SEOType;
use PrestaShopBundle\Form\Admin\Sell\Product\Shipping\ShippingType;
use PrestaShopBundle\Form\Admin\Sell\Product\Specification\SpecificationsType;
use PrestaShopBundle\Form\Admin\Sell\Product\Stock\StockType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * This is the parent product form type
 */
class ProductFormType extends TranslatorAwareType
{
    /**
     * @var EventSubscriberInterface
     */
    private $productTypeListener;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param EventSubscriberInterface $productTypeListener
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        EventSubscriberInterface $productTypeListener
    ) {
        parent::__construct($translator, $locales);
        $this->productTypeListener = $productTypeListener;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $productId = $options['product_id'] ?? null;
        $builder
            ->add('header', HeaderType::class)
            ->add('description', DescriptionType::class, [
                'product_id' => $productId,
            ])
            ->add('specifications', SpecificationsType::class)
            ->add('stock', StockType::class, [
                'product_id' => $productId,
                'virtual_product_file_id' => $options['virtual_product_file_id'],
            ])
            ->add('shipping', ShippingType::class)
            ->add('pricing', PricingType::class)
            ->add('seo', SEOType::class)
            ->add('options', OptionsType::class)
            ->add('footer', FooterType::class, [
                'product_id' => $productId,
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
        // Important to get data from form and not options as it's the most up to date
        $formData = $form->getData();
        $productType = $formData['header']['type'] ?? ProductType::TYPE_STANDARD;
        $formVars = [
            'product_type' => $productType,
            'product_id' => isset($options['product_id']) ? (int) $options['product_id'] : null,
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
        $resolver->setDefaults([
            'product_id' => null,
            'product_type' => null,
            'virtual_product_file_id' => null,
            'allow_extra_fields' => true,
        ]);
        $resolver->setAllowedTypes('product_id', ['null', 'int']);
        $resolver->setAllowedTypes('product_type', ['null', 'string']);
        $resolver->setAllowedTypes('virtual_product_file_id', ['null', 'int']);
    }

    /**
     * {@inheritDoc}
     */
    public function getBlockPrefix()
    {
        return 'product';
    }
}
