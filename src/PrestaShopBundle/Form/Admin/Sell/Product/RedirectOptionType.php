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

use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\RedirectType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use PrestaShopBundle\Form\Admin\Type\TypeaheadProductCollectionType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\EventListener\TransformationFailureListener;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

class RedirectOptionType extends TranslatorAwareType
{
    /**
     * @var LegacyContext
     */
    private $context;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var DataTransformerInterface
     */
    private $targetTransformer;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param LegacyContext $context
     * @param RouterInterface $router
     * @param DataTransformerInterface $targetTransformer
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        LegacyContext $context,
        RouterInterface $router,
        DataTransformerInterface $targetTransformer
    ) {
        parent::__construct($translator, $locales);
        $this->context = $context;
        $this->router = $router;
        $this->targetTransformer = $targetTransformer;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $productSearchUrl = $this->context->getLegacyAdminLink('AdminProducts', true, ['ajax' => 1, 'action' => 'productsList', 'forceJson' => 1, 'disableCombination' => 1, 'exclude_packs' => 0, 'excludeVirtuals' => 0, 'limit' => 20]) . '&q=%QUERY';
        $categorySearchUrl = $this->router->generate('admin_get_ajax_categories', ['query' => '%QUERY']);

        $builder
            ->add('type', ChoiceType::class, [
                'label' => $this->trans('Redirection when offline', 'Admin.Catalog.Feature'),
                'required' => false,
                'placeholder' => false, // Guaranties that no empty value is added in options
                'choices' => [
                    $this->trans('No redirection (404)', 'Admin.Catalog.Feature') => RedirectType::TYPE_NOT_FOUND,
                    $this->trans('Permanent redirection to a category (301)', 'Admin.Catalog.Feature') => RedirectType::TYPE_CATEGORY_PERMANENT,
                    $this->trans('Temporary redirection to a category (302)', 'Admin.Catalog.Feature') => RedirectType::TYPE_CATEGORY_TEMPORARY,
                    $this->trans('Permanent redirection to a product (301)', 'Admin.Catalog.Feature') => RedirectType::TYPE_PRODUCT_PERMANENT,
                    $this->trans('Temporary redirection to a product (302)', 'Admin.Catalog.Feature') => RedirectType::TYPE_PRODUCT_TEMPORARY,
                ],
            ])
            ->add('target', TypeaheadProductCollectionType::class, [
                'label' => $this->trans('Target product', 'Admin.Catalog.Feature'),
                'required' => false,
                'remote_url' => $productSearchUrl,
                'template_collection' => '<span class="label">%s</span>',
                'limit' => 1,
                'placeholder' => $this->trans('To which product the page should redirect?', 'Admin.Catalog.Help'),
                'help' => '',
                'error_bubbling' => false,
                'attr' => [
                    'data-product-label' => $this->trans('Target product', 'Admin.Catalog.Feature'),
                    'data-product-placeholder' => $this->trans('To which product the page should redirect?', 'Admin.Catalog.Help'),
                    'data-product-search-url' => $productSearchUrl,
                    'data-category-label' => $this->trans('Target category', 'Admin.Catalog.Feature'),
                    'data-category-placeholder' => $this->trans('To which category the page should redirect?', 'Admin.Catalog.Help'),
                    'data-category-hint' => $this->trans('If no category is selected the Main Category is used', 'Admin.Catalog.Help'),
                    'data-category-search-url' => $categorySearchUrl,
                ],
            ])
        ;

        // This will transform the target ID from model data into an array adapted for TypeaheadProductCollectionType
        $builder->get('target')->addModelTransformer($this->targetTransformer);
        // In case a transformation occurs it will be displayed as an inline error
        $builder->addEventSubscriber(new TransformationFailureListener($this->translator));
    }
}
