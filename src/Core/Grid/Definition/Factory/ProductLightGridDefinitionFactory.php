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

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Grid\Action\ViewOptionsCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\LinkColumn;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;

class ProductLightGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    /**
     * Uses original product grid id to keep latest filters and sorting
     */
    public const GRID_ID = ProductGridDefinitionFactory::GRID_ID;

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @param HookDispatcherInterface $hookDispatcher
     * @param ConfigurationInterface $configuration
     */
    public function __construct(
        HookDispatcherInterface $hookDispatcher,
        ConfigurationInterface $configuration
    ) {
        parent::__construct($hookDispatcher);
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    protected function getId(): string
    {
        return self::GRID_ID;
    }

    /**
     * {@inheritdoc}
     */
    protected function getName(): string
    {
        return $this->trans('Products', [], 'Admin.Navigation.Menu');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        $columns = (new ColumnCollection())
            ->add((new LinkColumn('id_product'))
            ->setName($this->trans('ID', [], 'Admin.Global'))
            ->setOptions([
                'field' => 'id_product',
                'sortable' => false,
                'route' => 'admin_products_v2_edit',
                'route_param_name' => 'productId',
                'route_param_field' => 'id_product',
            ])
            )
            ->add((new LinkColumn('name'))
            ->setName($this->trans('Name', [], 'Admin.Global'))
            ->setOptions([
                'field' => 'name',
                'route' => 'admin_products_v2_edit',
                'route_param_name' => 'productId',
                'route_param_field' => 'id_product',
                'sortable' => false,
            ])
            )
            ->add((new LinkColumn('price_tax_excluded'))
            ->setName($this->trans('Price', [], 'Admin.Global'))
            ->setOptions([
                'field' => 'price_tax_excluded',
                'route' => 'admin_products_v2_edit',
                'route_param_name' => 'productId',
                'route_param_field' => 'id_product',
                'route_fragment' => 'tab-product_pricing-tab',
                'sortable' => false,
            ])
            );

        if ($this->configuration->get('PS_STOCK_MANAGEMENT')) {
            $columns->add((new LinkColumn('quantity'))
                ->setName($this->trans('Quantity', [], 'Admin.Catalog.Feature'))
                ->setOptions([
                    'field' => 'quantity',
                    'route' => 'admin_products_v2_edit',
                    'route_param_name' => 'productId',
                    'route_param_field' => 'id_product',
                    'route_fragment' => 'tab-product_stock-tab',
                    'sortable' => false,
                    'color_template_field' => 'quantity_color',
                ])
            );
        }

        return $columns;
    }

    /**
     * {@inheritDoc}
     */
    protected function getViewOptions()
    {
        return (new ViewOptionsCollection())->add('pagination_view', 'quicknav');
    }
}
