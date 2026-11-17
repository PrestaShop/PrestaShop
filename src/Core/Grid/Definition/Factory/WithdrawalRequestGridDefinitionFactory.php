<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Form\ChoiceProvider\WithdrawalRequestStatusChoiceProvider;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\SubmitRowAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BadgeColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DateTimeColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollectionInterface;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Defines the "Customers > Customer Service > Withdrawal requests" grid.
 */
final class WithdrawalRequestGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    public const GRID_ID = 'withdrawal_request';

    public function __construct(
        HookDispatcherInterface $hookDispatcher,
        private readonly ShopContext $shopContext,
        private readonly WithdrawalRequestStatusChoiceProvider $statusChoiceProvider,
    ) {
        parent::__construct($hookDispatcher);
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
        return $this->trans('Withdrawal requests', [], 'Admin.Navigation.Menu');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns(): ColumnCollection
    {
        $columns = (new ColumnCollection())
            ->add(
                (new DataColumn('id_withdrawal_request'))
                    ->setName($this->trans('ID', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'id_withdrawal_request',
                    ])
            )
            ->add(
                (new DataColumn('order_reference'))
                    ->setName($this->trans('Order', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'order_reference',
                    ])
            )
            ->add(
                (new DataColumn('customer'))
                    ->setName($this->trans('Customer', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'customer',
                    ])
            )
            ->add(
                (new DataColumn('email'))
                    ->setName($this->trans('Email', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'email',
                    ])
            )
            ->add(
                (new DateTimeColumn('submitted_at'))
                    ->setName($this->trans('Submitted on', [], 'Admin.Orderscustomers.Feature'))
                    ->setOptions([
                        'field' => 'submitted_at',
                        'format' => DateTimeColumn::DEFAULT_FORMAT,
                    ])
            );

        if ($this->shopContext->isMultiShopUsed()) {
            $columns->add(
                (new DataColumn('shop_name'))
                    ->setName($this->trans('Store', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'shop_name',
                    ])
            );
        }

        return $columns
            ->add(
                (new BadgeColumn('status'))
                    ->setName($this->trans('Status', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'status_label',
                        'alignment' => 'left',
                        'badge_type' => '',
                        'badge_type_field' => 'status_badge_type',
                    ])
            )
            ->add(
                (new ActionColumn('actions'))
                    ->setName($this->trans('Actions', [], 'Admin.Global'))
                    ->setOptions([
                        'actions' => (new RowActionCollection())
                            ->add(
                                (new LinkRowAction('view_order'))
                                    ->setName($this->trans('View order', [], 'Admin.Orderscustomers.Feature'))
                                    ->setIcon('visibility')
                                    ->setOptions([
                                        'route' => 'admin_orders_view',
                                        'route_param_name' => 'orderId',
                                        'route_param_field' => 'id_order',
                                        'clickable_row' => true,
                                    ])
                            )
                            ->add(
                                (new SubmitRowAction('mark_processed'))
                                    ->setName($this->trans('Mark as processed', [], 'Admin.Orderscustomers.Feature'))
                                    ->setIcon('check')
                                    ->setOptions([
                                        'method' => 'POST',
                                        'route' => 'admin_withdrawal_requests_mark_processed',
                                        'route_param_name' => 'withdrawalRequestId',
                                        'route_param_field' => 'id_withdrawal_request',
                                    ])
                            )
                            ->add(
                                (new SubmitRowAction('mark_refused'))
                                    ->setName($this->trans('Mark as refused', [], 'Admin.Orderscustomers.Feature'))
                                    ->setIcon('block')
                                    ->setOptions([
                                        'method' => 'POST',
                                        'route' => 'admin_withdrawal_requests_mark_refused',
                                        'route_param_name' => 'withdrawalRequestId',
                                        'route_param_field' => 'id_withdrawal_request',
                                        'confirm_message' => $this->trans(
                                            'Withdrawal is a right of the consumer: only refuse a request when the order falls under a legal exception. Continue?',
                                            [],
                                            'Admin.Orderscustomers.Notification'
                                        ),
                                    ])
                            ),
                    ])
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function getFilters(): FilterCollectionInterface
    {
        $filters = (new FilterCollection())
            ->add(
                (new Filter('id_withdrawal_request', NumberType::class))
                    ->setTypeOptions([
                        'attr' => [
                            'placeholder' => $this->trans('Search ID', [], 'Admin.Actions'),
                        ],
                        'required' => false,
                    ])
                    ->setAssociatedColumn('id_withdrawal_request')
            )
            ->add(
                (new Filter('order_reference', TextType::class))
                    ->setTypeOptions([
                        'attr' => [
                            'placeholder' => $this->trans('Search reference', [], 'Admin.Actions'),
                        ],
                        'required' => false,
                    ])
                    ->setAssociatedColumn('order_reference')
            )
            ->add(
                (new Filter('customer', TextType::class))
                    ->setTypeOptions([
                        'attr' => [
                            'placeholder' => $this->trans('Search name', [], 'Admin.Actions'),
                        ],
                        'required' => false,
                    ])
                    ->setAssociatedColumn('customer')
            )
            ->add(
                (new Filter('email', TextType::class))
                    ->setTypeOptions([
                        'attr' => [
                            'placeholder' => $this->trans('Search email', [], 'Admin.Actions'),
                        ],
                        'required' => false,
                    ])
                    ->setAssociatedColumn('email')
            );

        if ($this->shopContext->isMultiShopUsed()) {
            $filters->add(
                (new Filter('shop_name', TextType::class))
                    ->setTypeOptions([
                        'attr' => [
                            'placeholder' => $this->trans('Search store', [], 'Admin.Actions'),
                        ],
                        'required' => false,
                    ])
                    ->setAssociatedColumn('shop_name')
            );
        }

        return $filters
            ->add(
                (new Filter('status', ChoiceType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'placeholder' => $this->trans('All', [], 'Admin.Global'),
                        'choices' => $this->statusChoiceProvider->getChoices(),
                    ])
                    ->setAssociatedColumn('status')
            )
            ->add(
                (new Filter('actions', SearchAndResetType::class))
                    ->setTypeOptions([
                        'reset_route' => 'admin_common_reset_search_by_filter_id',
                        'reset_route_params' => [
                            'filterId' => self::GRID_ID,
                        ],
                        'redirect_route' => 'admin_withdrawal_requests_index',
                    ])
                    ->setAssociatedColumn('actions')
            );
    }
}
