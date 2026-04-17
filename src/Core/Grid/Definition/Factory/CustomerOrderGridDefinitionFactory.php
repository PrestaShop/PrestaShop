<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\ViewOptionsCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DateTimeColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\HtmlColumn;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;

/**
 * Class CustomerOrderGridDefinitionFactory defines customer's order grid structure.
 */
final class CustomerOrderGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    use DeleteActionTrait;

    public const GRID_ID = 'customer_order';

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
        return $this->trans('Orders', [], 'Admin.Global');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        return (new ColumnCollection())
            ->add((new DataColumn('id_order'))
                ->setName($this->trans('ID', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'id_order',
                ])
            )
            ->add((new DateTimeColumn('date_add'))
                ->setName($this->trans('Date', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'date_add',
                    'format' => $this->contextDateFormat,
                    'clickable' => true,
                ])
            )
            ->add((new DataColumn('payment'))
                ->setName($this->trans('Payment', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'payment',
                ])
            )
            ->add((new HtmlColumn('status'))
                ->setName($this->trans('Status', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'status',
                ])
            )
            ->add((new DataColumn('nb_products'))
                ->setName($this->trans('Products', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'nb_products',
                ])
            )
            ->add((new DataColumn('total_paid_tax_incl'))
                ->setName($this->trans('Total (Tax incl.)', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'total_paid_tax_incl',
                ])
            )
            ->add((new ActionColumn('actions'))
                ->setName($this->trans('Actions', [], 'Admin.Global'))
                ->setOptions([
                    'actions' => (new RowActionCollection())
                        ->add(
                            (new LinkRowAction('view'))
                                ->setName($this->trans('View', [], 'Admin.Actions'))
                                ->setIcon('zoom_in')
                                ->setOptions([
                                    'route' => 'admin_orders_view',
                                    'route_param_name' => 'orderId',
                                    'route_param_field' => 'id_order',
                                    'use_inline_display' => true,
                                    'clickable_row' => true,
                                ])
                        ),
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
