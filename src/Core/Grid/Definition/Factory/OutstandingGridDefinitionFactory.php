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
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BadgeColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DateTimeColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShopBundle\Form\Admin\Type\DateRangeType;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use Risk;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Created definition for Outstanding grid.
 */
final class OutstandingGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    public const GRID_ID = 'outstanding';

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var array<string, int>
     */
    private $risks;

    /**
     * @var string
     */
    private $contextDateFormat;

    /**
     * @param HookDispatcherInterface $hookDispatcher
     * @param ConfigurationInterface $configuration
     * @param int $languageId
     * @param string $contextDateFormat
     */
    public function __construct(
        HookDispatcherInterface $hookDispatcher,
        ConfigurationInterface $configuration,
        int $languageId,
        string $contextDateFormat
    ) {
        parent::__construct($hookDispatcher);

        $this->configuration = $configuration;
        $this->contextDateFormat = $contextDateFormat;
        foreach (Risk::getRisks($languageId) as $risk) {
            /* @var $risk Risk */
            $this->risks[$risk->name] = $risk->id;
        }
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
        return $this->trans('Outstanding', [], 'Admin.Navigation.Menu');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        $columns = (new ColumnCollection())
            ->add(
                (new DataColumn('id_invoice'))
                    ->setName($this->trans('ID', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'id_invoice',
                    ])
            )
            ->add(
                (new DateTimeColumn('date_add'))
                    ->setName($this->trans('Date', [], 'Admin.Global'))
                    ->setOptions([
                        'format' => $this->contextDateFormat,
                        'field' => 'date_add',
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
                (new DataColumn('company'))
                    ->setName($this->trans('Company', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'company',
                    ])
            )
            ->add(
                (new BadgeColumn('risk'))
                    ->setName($this->trans('Risk', [], 'Admin.Orderscustomers.Feature'))
                    ->setOptions([
                        'field' => 'risk',
                        'color_field' => 'color',
                        'badge_type' => '',
                        'sortable' => false,
                        'empty_value' => '--',
                    ])
            )
            ->add(
                (new DataColumn('outstanding_allow_amount'))
                    ->setName($this->trans('Outstanding allowance', [], 'Admin.Orderscustomers.Feature'))
                    ->setOptions([
                        'field' => 'outstanding_allow_amount',
                    ])
            )
            ->add(
                (new DataColumn('outstanding'))
                    ->setName($this->trans('Current outstanding', [], 'Admin.Orderscustomers.Feature'))
                    ->setOptions([
                        'field' => 'outstanding',
                        'sortable' => false,
                    ])
            )
            ->add(
                (new ActionColumn('actions'))
                    ->setName($this->trans('Actions', [], 'Admin.Global'))
                    ->setOptions([
                        'actions' => $this->getViewRowAction(),
                    ])
            );

        if ($this->configuration->get('PS_INVOICE')) {
            $columns->addBefore(
                'actions',
                (new ActionColumn('invoice'))
                    ->setName($this->trans('Invoice', [], 'Admin.Global'))
                    ->setOptions([
                        'actions' => $this->getInvoiceRowAction(),
                    ])
            );
        }

        return $columns;
    }

    protected function getFilters()
    {
        return (new FilterCollection())
            ->add(
                (new Filter('id_invoice', TextType::class))
                    ->setAssociatedColumn('id_invoice')
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => [
                            'placeholder' => $this->trans('Search ID', [], 'Admin.Actions'),
                        ],
                    ])
            )
            ->add(
                (new Filter('date_add', DateRangeType::class))
                    ->setAssociatedColumn('date_add')
                    ->setTypeOptions([
                        'required' => false,
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
                (new Filter('company', TextType::class))
                    ->setAssociatedColumn('company')
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => [
                            'placeholder' => $this->trans('Search company', [], 'Admin.Actions'),
                        ],
                    ])
            )
            ->add(
                (new Filter('risk', ChoiceType::class))
                    ->setAssociatedColumn('risk')
                    ->setTypeOptions([
                        'required' => false,
                        'choices' => $this->risks,
                    ])
            )
            ->add(
                (new Filter('outstanding_allow_amount', TextType::class))
                    ->setAssociatedColumn('outstanding_allow_amount')
                    ->setTypeOptions([
                        'required' => false,
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
                        'redirect_route' => 'admin_outstanding_index',
                    ])
            );
    }

    private function getInvoiceRowAction(): RowActionCollection
    {
        $collection = new RowActionCollection();
        $collection
            ->add(
                (new LinkRowAction('print_invoice'))
                    ->setName($this->trans('View invoice', [], 'Admin.Orderscustomers.Feature'))
                    ->setIcon('receipt')
                    ->setOptions([
                        'route' => 'admin_order_invoices_generate_by_id',
                        'route_param_name' => 'invoiceId',
                        'route_param_field' => 'id_invoice',
                        'use_inline_display' => true,
                    ])
            );

        return $collection;
    }

    private function getViewRowAction(): RowActionCollection
    {
        $collection = new RowActionCollection();
        $collection
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
            );

        return $collection;
    }
}
