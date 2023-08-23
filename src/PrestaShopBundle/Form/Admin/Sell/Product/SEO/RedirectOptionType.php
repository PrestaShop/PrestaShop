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

namespace PrestaShopBundle\Form\Admin\Sell\Product\SEO;

use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\RedirectType;
use PrestaShopBundle\Form\Admin\Type\EntitySearchInputType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\EventListener\TransformationFailureListener;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class RedirectOptionType extends TranslatorAwareType
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var DataTransformerInterface
     */
    private $targetTransformer;

    /**
     * @var EventSubscriberInterface
     */
    private $eventSubscriber;

    /**
     * @var string
     */
    private $employeeIsoCode;

    /**
     * @var int
     */
    private $homeCategoryId;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param RouterInterface $router
     * @param DataTransformerInterface $targetTransformer
     * @param EventSubscriberInterface $eventSubscriber
     * @param string $employeeIsoCode
     * @param int $homeCategoryId
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        RouterInterface $router,
        DataTransformerInterface $targetTransformer,
        EventSubscriberInterface $eventSubscriber,
        string $employeeIsoCode,
        int $homeCategoryId
    ) {
        parent::__construct($translator, $locales);
        $this->router = $router;
        $this->targetTransformer = $targetTransformer;
        $this->eventSubscriber = $eventSubscriber;
        $this->employeeIsoCode = $employeeIsoCode;
        $this->homeCategoryId = $homeCategoryId;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $entityAttributes = [
            'product' => [
                'label' => $this->trans('Target product', 'Admin.Catalog.Feature'),
                'placeholder' => $this->trans('To which product should the page redirect?', 'Admin.Catalog.Help'),
                'help' => '',
                'searchUrl' => $this->router->generate('admin_products_search_products_for_association', [
                    'languageCode' => $this->employeeIsoCode,
                    'query' => '__QUERY__',
                ]),
                'filtered' => json_encode([$options['product_id']]),
            ],
            'category' => [
                'label' => $this->trans('Target category', 'Admin.Catalog.Feature'),
                'placeholder' => $this->trans('To which category should the page redirect?', 'Admin.Catalog.Help'),
                'help' => $this->trans('By default, the main category will be used if no category is selected.', 'Admin.Catalog.Help'),
                'searchUrl' => $this->router->generate('admin_categories_get_ajax_categories', ['query' => '__QUERY__']),
                'filtered' => json_encode([$this->homeCategoryId]),
            ],
        ];
        $defaultEntity = 'product';

        $builder
            ->add('type', ChoiceType::class, [
                'label' => $this->trans('Redirection when offline', 'Admin.Catalog.Feature'),
                'required' => false,
                'placeholder' => false, // Guaranties that no empty value is added in options
                'choices' => [
                    $this->trans('Default behavior from configuration', 'Admin.Catalog.Feature') => RedirectType::TYPE_DEFAULT,
                    $this->trans('No redirection (200), display product', 'Admin.Catalog.Feature') => RedirectType::TYPE_SUCCESS_DISPLAYED,
                    $this->trans('No redirection (404), display product', 'Admin.Catalog.Feature') => RedirectType::TYPE_NOT_FOUND_DISPLAYED,
                    $this->trans('No redirection (410), display product', 'Admin.Catalog.Feature') => RedirectType::TYPE_GONE_DISPLAYED,
                    $this->trans('No redirection (404), display error page', 'Admin.Catalog.Feature') => RedirectType::TYPE_NOT_FOUND,
                    $this->trans('No redirection (410), display error page', 'Admin.Catalog.Feature') => RedirectType::TYPE_GONE,
                    $this->trans('Permanent redirection to a category (301)', 'Admin.Catalog.Feature') => RedirectType::TYPE_CATEGORY_PERMANENT,
                    $this->trans('Temporary redirection to a category (302)', 'Admin.Catalog.Feature') => RedirectType::TYPE_CATEGORY_TEMPORARY,
                    $this->trans('Permanent redirection to a product (301)', 'Admin.Catalog.Feature') => RedirectType::TYPE_PRODUCT_PERMANENT,
                    $this->trans('Temporary redirection to a product (302)', 'Admin.Catalog.Feature') => RedirectType::TYPE_PRODUCT_TEMPORARY,
                ],
                'modify_all_shops' => true,
                'external_link' => [
                    'text' => $this->trans('[1]Edit default behavior[/1]', 'Admin.Catalog.Feature'),
                    'href' => $this->router->generate('admin_product_preferences') . '#configuration_fieldset_products',
                ],
            ])
            ->add('target', EntitySearchInputType::class, [
                'required' => false,
                'limit' => 1,
                'min_length' => 3,
                'label' => $entityAttributes[$defaultEntity]['label'],
                'remote_url' => $entityAttributes[$defaultEntity]['searchUrl'],
                'placeholder' => $entityAttributes[$defaultEntity]['placeholder'],
                'help' => $entityAttributes[$defaultEntity]['help'],
                'attr' => [
                    'data-product-label' => $entityAttributes['product']['label'],
                    'data-product-placeholder' => $entityAttributes['product']['placeholder'],
                    'data-product-search-url' => $entityAttributes['product']['searchUrl'],
                    'data-product-help' => $entityAttributes['product']['help'],
                    'data-product-filtered' => $entityAttributes['product']['filtered'],
                    'data-category-label' => $entityAttributes['category']['label'],
                    'data-category-placeholder' => $entityAttributes['category']['placeholder'],
                    'data-category-search-url' => $entityAttributes['category']['searchUrl'],
                    'data-category-help' => $entityAttributes['category']['help'],
                    'data-category-filtered' => $entityAttributes['category']['filtered'],
                ],
                'modify_all_shops' => true,
            ])
        ;

        // This will transform the target ID from model data into an array adapted for EntitySearchInputType
        $builder->addModelTransformer($this->targetTransformer);
        // In case a transformation occurs it will be displayed as an inline error
        $builder->addEventSubscriber(new TransformationFailureListener($this->getTranslator()));

        // Preset the input attributes correctly depending on the data
        $builder->addEventSubscriber($this->eventSubscriber);
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver
            ->setDefaults([
                'required' => false,
                'label' => $this->trans('Redirection page', 'Admin.Catalog.Feature'),
                'label_tag_name' => 'h3',
                'label_help_box' => $this->trans('When your product is disabled, choose to which page you’d like to redirect the customers visiting its page by typing the product or category name.', 'Admin.Catalog.Help'),
                'columns_number' => 2,
                'row_attr' => [
                    'class' => 'redirect-option-widget',
                ],
                'alert_title' => $this->trans('Default behavior is specified in the Shop Parameters > Product settings page.', 'Admin.Catalog.Help'),
                'alert_message' => $this->getRedirectionAlertMessages(),
            ])
            ->setRequired([
                'product_id',
            ])
            ->setAllowedTypes('product_id', 'int')
        ;
    }

    /**
     * @return array
     */
    private function getRedirectionAlertMessages(): array
    {
        $formatParameters = [
            '[1]' => '<strong>',
            '[/1]' => '</strong>',
            '[2]' => '<br>',
        ];

        return [
            $this->trans('[1]No redirection (200), display product[/1] [2] Do not redirect anywhere, display product as discontinued and return normal 200 response.', 'Admin.Catalog.Help', $formatParameters),
            $this->trans('[1]No redirection (404), display product[/1] [2] Do not redirect anywhere, display product as discontinued and return 404 "Not Found" response.', 'Admin.Catalog.Help', $formatParameters),
            $this->trans('[1]No redirection (410), display product[/1] [2] Do not redirect anywhere, display product as discontinued and return 410 "Gone" response.', 'Admin.Catalog.Help', $formatParameters),
            $this->trans('[1]No redirection (404), display error page[/1] [2] Do not redirect anywhere and display a 404 "Not Found" page.', 'Admin.Catalog.Help', $formatParameters),
            $this->trans('[1]No redirection (410), display error page[/1] [2] Do not redirect anywhere and display a 410 "Gone" page.', 'Admin.Catalog.Help', $formatParameters),
            $this->trans('[1]Permanent redirection (301)[/1] [2] Permanently display another product or category instead.', 'Admin.Catalog.Help', $formatParameters),
            $this->trans('[1]Temporary redirection (302)[/1] [2] Temporarily display another product or category instead.', 'Admin.Catalog.Help', $formatParameters),
        ];
    }
}
