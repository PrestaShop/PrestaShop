<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
                    'route' => 'admin_products_edit',
                    'route_param_name' => 'productId',
                    'route_param_field' => 'id_product',
                ])
            )
            ->add((new LinkColumn('name'))
                ->setName($this->trans('Name', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'name',
                    'route' => 'admin_products_edit',
                    'route_param_name' => 'productId',
                    'route_param_field' => 'id_product',
                    'sortable' => false,
                ])
            )
            ->add((new LinkColumn('price_tax_excluded'))
                ->setName($this->trans('Price', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'price_tax_excluded',
                    'route' => 'admin_products_edit',
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
                    'route' => 'admin_products_edit',
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
