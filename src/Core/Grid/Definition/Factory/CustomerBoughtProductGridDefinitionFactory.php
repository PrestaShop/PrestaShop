<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\Grid\Action\ViewOptionsCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DateTimeColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\LinkColumn;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;

/**
 * Class CustomerBoughtProductGridDefinitionFactory defines customer's bought products grid structure.
 */
final class CustomerBoughtProductGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    use DeleteActionTrait;

    public const GRID_ID = 'customer_bought_product';

    /**
     * @var string
     */
    private $contextDateFormat;

    /**
     * @param HookDispatcherInterface $hookDispatcher
     * @param string $contextDateFormat
     */
    public function __construct(
        HookDispatcherInterface $hookDispatcher,
        $contextDateFormat
    ) {
        parent::__construct($hookDispatcher);
        $this->contextDateFormat = $contextDateFormat;
    }

    /**
     * {@inheritdoc}
     */
    protected function getId()
    {
        return self::GRID_ID;
    }

    /**
     * {@inheritdoc}
     */
    protected function getName()
    {
        return $this->trans('Purchased products', [], 'Admin.Orderscustomers.Feature');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        return (new ColumnCollection())
            ->add((new DateTimeColumn('date_add'))
                ->setName($this->trans('Date', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'date_add',
                    'format' => $this->contextDateFormat,
                    'clickable' => true,
                ])
            )
            ->add(
                (new LinkColumn('product_name'))
                    ->setName($this->trans('Name', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'product_name',
                        'route' => 'admin_orders_view',
                        'route_param_name' => 'orderId',
                        'route_param_field' => 'id_order',
                    ])
            )
            ->add((new DataColumn('product_quantity'))
                ->setName($this->trans('Quantity', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'product_quantity',
                ])
            )
            ->add(
                (new LinkColumn('id_order'))
                    ->setName($this->trans('Order ID', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'id_order',
                        'route' => 'admin_orders_view',
                        'route_param_name' => 'orderId',
                        'route_param_field' => 'id_order',
                    ])
            );
    }

    /**
     * {@inheritdoc}
     */
    public function getViewOptions()
    {
        return (new ViewOptionsCollection())
            ->add('display_name', false);
    }
}
