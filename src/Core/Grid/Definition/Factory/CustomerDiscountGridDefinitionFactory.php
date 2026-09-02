<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\Grid\Action\ModalOptions;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\Customer\DeleteCustomerDiscountRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\Customer\EditCustomerDiscountRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\ViewOptionsCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\StatusColumn;

/**
 * Class CustomerDiscountGridDefinitionFactory defines customer's discounts grid structure.
 */
final class CustomerDiscountGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    public const GRID_ID = 'customer_discount';

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
        return $this->trans('Vouchers', [], 'Admin.Orderscustomers.Feature');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        return (new ColumnCollection())
            ->add(
                (new DataColumn('id_cart_rule'))
                    ->setName($this->trans('ID', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'id_cart_rule',
                        'sortable' => false,
                    ])
            )
            ->add(
                (new DataColumn('code'))
                    ->setName($this->trans('Code', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'code',
                        'sortable' => false,
                    ])
            )
            ->add(
                (new DataColumn('name'))
                    ->setName($this->trans('Name', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'name',
                        'sortable' => false,
                    ])
            )
            ->add(
                (new StatusColumn('active'))
                    ->setName($this->trans('Status', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'active',
                        'sortable' => false,
                    ])
            )
            ->add(
                (new DataColumn('quantity'))
                    ->setName($this->trans('Qty available', [], 'Admin.Orderscustomers.Feature'))
                    ->setOptions([
                        'field' => 'quantity',
                        'sortable' => false,
                    ])
            )
            ->add((new ActionColumn('actions'))
                ->setName($this->trans('Actions', [], 'Admin.Global'))
                ->setOptions([
                    'actions' => (new RowActionCollection())
                        ->add(
                            (new EditCustomerDiscountRowAction('edit'))
                                ->setName($this->trans('Edit', [], 'Admin.Actions'))
                                ->setIcon('edit')
                                ->setOptions([
                                    'id_cart_rule' => 'id_cart_rule',
                                ])
                        )
                        ->add(
                            (new DeleteCustomerDiscountRowAction('delete'))
                                ->setName($this->trans('Delete', [], 'Admin.Actions'))
                                ->setIcon('delete')
                                ->setOptions([
                                    'id_cart_rule' => 'id_cart_rule',
                                    'confirm_message' => $this->trans('Are you sure you want to delete the selected item(s)?', [], 'Admin.Global'),
                                    'method' => 'POST',
                                    'modal_options' => new ModalOptions([
                                        'title' => $this->trans('Delete selection', [], 'Admin.Actions'),
                                        'confirm_button_label' => $this->trans('Delete', [], 'Admin.Actions'),
                                        'close_button_label' => $this->trans('Cancel', [], 'Admin.Actions'),
                                        'confirm_button_class' => 'btn-danger',
                                    ]),
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
