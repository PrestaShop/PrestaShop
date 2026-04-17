<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\Grid\Action\ViewOptionsCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DateTimeColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\LinkColumn;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;

/**
 * Class CustomerViewedProductGridDefinitionFactory defines customer's viewed products grid structure.
 */
final class CustomerViewedProductGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    use DeleteActionTrait;

    public const GRID_ID = 'customer_viewed_product';

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
        return $this->trans('Products in carts', [], 'Admin.Orderscustomers.Feature');
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
                        'route' => 'admin_products_preview',
                        'route_param_name' => 'productId',
                        'route_param_field' => 'id_product',
                    ])
            )
            ->add(
                (new LinkColumn('id_cart'))
                    ->setName($this->trans('Cart ID', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'id_cart',
                        'route' => 'admin_carts_view',
                        'route_param_name' => 'cartId',
                        'route_param_field' => 'id_cart',
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
