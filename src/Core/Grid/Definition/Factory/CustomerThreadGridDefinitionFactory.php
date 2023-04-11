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

use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\SimpleGridAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ChoiceColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\PrivateColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollectionInterface;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShopBundle\Form\Admin\Type\DateRangeType;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use PrestaShopBundle\Form\Admin\Type\YesAndNoChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CustomerThreadGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    use BulkDeleteActionTrait;
    use DeleteActionTrait;

    public const GRID_ID = 'customer_thread';

    /**
     * @var FormChoiceProviderInterface
     */
    private $contactTypeProvider;

    /**
     * @var FormChoiceProviderInterface
     */
    private $shopNameByIdChoiceProvider;

    /**
     * @var ConfigurableFormChoiceProviderInterface
     */
    private $customerThreadStatusesChoiceProvider;

    public function __construct(
        HookDispatcherInterface $hookDispatcher = null,
        FormChoiceProviderInterface $contactTypeProvider,
        FormChoiceProviderInterface $shopNameByIdChoiceProvider,
        ConfigurableFormChoiceProviderInterface $customerThreadStatusesChoiceProvider
    ) {
        parent::__construct($hookDispatcher);
        $this->contactTypeProvider = $contactTypeProvider;
        $this->shopNameByIdChoiceProvider = $shopNameByIdChoiceProvider;
        $this->customerThreadStatusesChoiceProvider = $customerThreadStatusesChoiceProvider;
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
        return $this->trans('Customer threads', [], 'Admin.Global');
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
                        'bulk_field' => 'id_customer_thread',
                    ])
            )
            ->add(
                (new DataColumn('id_customer_thread'))
                    ->setName($this->trans('ID', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'id_customer_thread',
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
                    ->setName($this->trans('Email', [], 'Admin.International.Feature'))
                    ->setOptions([
                        'field' => 'email',
                    ])
            )
            ->add(
                (new DataColumn('contact'))
                    ->setName($this->trans('Type', [], 'Admin.International.Feature'))
                    ->setOptions([
                        'field' => 'contact',
                    ])
            )
            ->add(
                (new DataColumn('langName'))
                    ->setName($this->trans('Language', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'langName',
                    ])
            )
            ->add(
                (new ChoiceColumn('status'))
                    ->setName($this->trans('Status', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'status',
                        'route' => 'admin_customer_threads_list_update_status',
                        'color_field' => 'status_color',
                        'choice_provider' => $this->customerThreadStatusesChoiceProvider,
                        'record_route_params' => [
                            'id_customer_thread' => 'customerThreadId',
                        ],
                    ])
            )
            ->add(
                (new DataColumn('employee'))
                    ->setName($this->trans('Employee', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'employee',
                    ])
            )
            ->add(
                (new DataColumn('message'))
                    ->setName($this->trans('Messages', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'message',
                        'max_displayed_characters' => 32,
                    ])
            )
            ->add(
                (new PrivateColumn('private'))
                    ->setName($this->trans('Private', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'private',
                    ])
            )
            ->add(
                (new DataColumn('date_upd'))
                    ->setName($this->trans('Last message', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'date_upd',
                    ])
            )
            ->add(
                (new DataColumn('shopName'))
                    ->setName($this->trans('Store', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'shopName',
                    ])
            )
            ->add(
                (new ActionColumn('actions'))
                    ->setName($this->trans('Actions', [], 'Admin.Global'))
                    ->setOptions([
                        'actions' => $this->getRowActions(),
                    ])
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function getFilters(): FilterCollectionInterface
    {
        return (new FilterCollection())
            ->add(
                (new Filter('id_customer_thread', TextType::class))
                    ->setAssociatedColumn('id_customer_thread')
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => [
                            'placeholder' => $this->trans('Search ID', [], 'Admin.Actions'),
                        ],
                    ])
            )
            ->add(
                (new Filter('customer', TextType::class))
                    ->setAssociatedColumn('customer')
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => [
                            'placeholder' => $this->trans('Search customer', [], 'Admin.Actions'),
                        ],
                    ])
            )
            ->add(
                (new Filter('email', TextType::class))
                    ->setAssociatedColumn('email')
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => [
                            'placeholder' => $this->trans('Search email', [], 'Admin.Actions'),
                        ],
                    ])
            )
            ->add(
                (new Filter('contact', ChoiceType::class))
                    ->setAssociatedColumn('contact')
                    ->setTypeOptions([
                        'required' => false,
                        'choices' => $this->contactTypeProvider->getChoices(),
                    ])
            )
            ->add(
                (new Filter('langName', TextType::class))
                    ->setAssociatedColumn('langName')
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => [
                            'placeholder' => $this->trans('Search language', [], 'Admin.Actions'),
                        ],
                    ])
            )
            ->add(
                (new Filter('status', ChoiceType::class))
                    ->setAssociatedColumn('status')
                    ->setTypeOptions([
                        'required' => false,
                        'choices' => $this->customerThreadStatusesChoiceProvider->getChoices([]),
                    ])
            )
            ->add(
                (new Filter('employee', TextType::class))
                    ->setAssociatedColumn('employee')
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => [
                            'placeholder' => $this->trans('Search employee', [], 'Admin.Actions'),
                        ],
                    ])
            )
            ->add(
                (new Filter('message', TextType::class))
                    ->setAssociatedColumn('message')
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => [
                            'placeholder' => $this->trans('Search message', [], 'Admin.Actions'),
                        ],
                    ])
            )
            ->add(
                (new Filter('private', YesAndNoChoiceType::class))
                    ->setAssociatedColumn('private')
                    ->setTypeOptions([
                        'required' => false,
                        'choice_translation_domain' => false,
                    ])
            )
            ->add(
                (new Filter('date_upd', DateRangeType::class))
                    ->setAssociatedColumn('date_upd')
                    ->setTypeOptions([
                        'required' => false,
                    ])
            )
            ->add(
                (new Filter('shopName', ChoiceType::class))
                    ->setAssociatedColumn('shopName')
                    ->setTypeOptions([
                        'required' => false,
                        'choices' => $this->shopNameByIdChoiceProvider->getChoices(),
                    ])
            )
            ->add(
                (new Filter('actions', SearchAndResetType::class))
                    ->setAssociatedColumn('actions')
                    ->setTypeOptions([
                        'reset_route' => 'admin_common_reset_search_by_filter_id',
                        'reset_route_params' => [
                            'filterId' => self::GRID_ID,
                        ],
                        'redirect_route' => 'admin_customer_threads',
                    ])
            );
    }

    /**
     * @return RowActionCollection
     */
    protected function getRowActions(): RowActionCollection
    {
        $rowActionCollection = new RowActionCollection();
        $rowActionCollection
            ->add(
                (new LinkRowAction('view'))
                    ->setName($this->trans('View', [], 'Admin.Actions'))
                    ->setIcon('zoom_in')
                    ->setOptions([
                        'route' => 'admin_customer_threads_view',
                        'route_param_name' => 'customerThreadId',
                        'route_param_field' => 'id_customer_thread',
                        'clickable_row' => true,
                    ])
            )
            ->add(
            $this->buildDeleteAction(
                'admin_customer_threads_delete',
                'customerThreadId',
                'id_customer_thread'
            )
        );

        return $rowActionCollection;
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
                    ->setName($this->trans('Export to SQL manager', [], 'Admin.Actions'))
                    ->setIcon('storage')
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function getBulkActions(): BulkActionCollectionInterface
    {
        return (new BulkActionCollection())
            ->add($this->buildBulkDeleteAction('admin_customer_threads_bulk_delete'));
    }
}
