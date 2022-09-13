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

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory\Security\Session;

use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\SimpleGridAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DateTimeColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\AbstractGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\BulkDeleteActionTrait;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\DeleteActionTrait;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollectionInterface;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShopBundle\Form\Admin\Type\DateRangeType;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class CustomerGridDefinitionFactory responsible for creating Customer sessions grid definition.
 */
final class CustomerGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    use BulkDeleteActionTrait;
    use DeleteActionTrait;

    /**
     * @var string Date format for the current user
     */
    private $contextDateFormat;

    public function __construct(
        HookDispatcherInterface $hookDispatcher,
        string $contextDateFormat
    ) {
        parent::__construct($hookDispatcher);
        $this->contextDateFormat = $contextDateFormat;
    }

    /**
     * @var string
     */
    public const GRID_ID = 'security_session_customer';

    /**
     * {@inheritdoc}
     */
    protected function getId(): string
    {
        return static::GRID_ID;
    }

    /**
     * {@inheritdoc}
     */
    protected function getName(): string
    {
        return $this->trans('Customer Sessions', [], 'Admin.Navigation.Menu');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns(): ColumnCollectionInterface
    {
        return (new ColumnCollection())
            ->add(
                (new BulkActionColumn('bulk'))
                    ->setOptions([
                        'bulk_field' => 'id_customer_session',
                    ])
            )
            ->add(
                (new DataColumn('id_customer_session'))
                    ->setName($this->trans('ID', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'id_customer_session',
                    ])
            )
            ->add(
                (new DataColumn('id_customer'))
                    ->setName($this->trans('Customer ID', [], 'Admin.Advparameters.Feature'))
                    ->setOptions([
                        'field' => 'id_customer',
                    ])
            )
            ->add(
                (new DataColumn('firstname'))
                    ->setName($this->trans('First name', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'firstname',
                    ])
            )
            ->add(
                (new DataColumn('lastname'))
                    ->setName($this->trans('Last name', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'lastname',
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
                (new DateTimeColumn('date_upd'))
                    ->setName($this->trans('Last update', [], 'Admin.Advparameters.Feature'))
                    ->setOptions([
                        'field' => 'date_upd',
                        'format' => $this->contextDateFormat,
                    ])
            )
            ->add(
                (new ActionColumn('actions'))
                    ->setName($this->trans('Actions', [], 'Admin.Global'))
                    ->setOptions([
                        'actions' => (new RowActionCollection())
                            ->add(
                                $this->buildDeleteAction(
                                    'admin_security_sessions_customer_delete',
                                    'sessionId',
                                    'id_customer_session',
                                    'DELETE',
                                    [],
                                    [
                                        'confirm_message' => $this->trans(
                                            'The user will be signed out from this session.',
                                            [],
                                            'Admin.Advparameters.Feature'
                                        ),
                                        'modal_options' => [
                                            'title' => $this->trans(
                                                'Delete session?',
                                                [],
                                                'Admin.Advparameters.Feature'
                                            ),
                                        ],
                                    ]
                                )
                            ),
                    ])
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function getGridActions(): GridActionCollectionInterface
    {
        return (new GridActionCollection())
            ->add(
                (new SimpleGridAction('common_refresh_list'))
                    ->setName($this->trans('Refresh list', [], 'Admin.Advparameters.Feature'))
                    ->setIcon('refresh')
            )
            ->add(
                (new SimpleGridAction('common_show_query'))
                    ->setName($this->trans('Show SQL query', [], 'Admin.Actions'))
                    ->setIcon('code')
            )
            ->add(
                (new SimpleGridAction('common_export_sql_manager'))
                    ->setName($this->trans('Export to SQL Manager', [], 'Admin.Actions'))
                    ->setIcon('storage')
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function getFilters(): FilterCollectionInterface
    {
        return (new FilterCollection())
            ->add(
                (new Filter('id_customer_session', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'size' => 'small',
                        'attr' => [
                            'placeholder' => $this->trans('Search ID', [], 'Admin.Actions'),
                        ],
                    ])
                    ->setAssociatedColumn('id_customer_session')
            )
            ->add(
                (new Filter('id_customer', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'size' => 'small',
                        'attr' => [
                            'placeholder' => $this->trans('Search ID', [], 'Admin.Actions'),
                        ],
                    ])
                    ->setAssociatedColumn('id_customer')
            )
            ->add(
                (new Filter('firstname', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => [
                            'placeholder' => $this->trans('First name', [], 'Admin.Global'),
                        ],
                    ])
                    ->setAssociatedColumn('firstname')
            )
            ->add(
                (new Filter('lastname', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => [
                            'placeholder' => $this->trans('Last name', [], 'Admin.Global'),
                        ],
                    ])
                    ->setAssociatedColumn('lastname')
            )
            ->add(
                (new Filter('email', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => [
                            'placeholder' => $this->trans('Email', [], 'Admin.Global'),
                        ],
                    ])
                    ->setAssociatedColumn('email')
            )
            ->add(
                (new Filter('date_upd', DateRangeType::class))
                    ->setTypeOptions([
                        'required' => false,
                    ])
                    ->setAssociatedColumn('date_upd')
            )
            ->add(
                (new Filter('actions', SearchAndResetType::class))
                    ->setTypeOptions([
                        'reset_route' => 'admin_common_reset_search_by_filter_id',
                        'reset_route_params' => [
                            'filterId' => self::GRID_ID,
                        ],
                        'redirect_route' => 'admin_security_sessions_customer_search',
                    ])
                    ->setAssociatedColumn('actions')
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function getBulkActions(): BulkActionCollectionInterface
    {
        return (new BulkActionCollection())
            ->add(
                $this->buildBulkDeleteAction(
                    'admin_security_sessions_customer_bulk_delete',
                    [
                        'confirm_message' => $this->trans(
                            'Users will be signed out from all selected sessions.',
                            [],
                            'Admin.Advparameters.Feature'
                        ),
                        'modal_options' => [
                            'title' => $this->trans(
                                'Delete selected sessions?',
                                [],
                                'Admin.Advparameters.Feature'
                            ),
                        ],
                    ]
                )
            );
    }
}
