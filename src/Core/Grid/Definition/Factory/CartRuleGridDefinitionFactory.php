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

use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\Type\SubmitBulkAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DateTimeColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ToggleColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollectionInterface;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShopBundle\Form\Admin\Type\DateRangeType;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use PrestaShopBundle\Form\Admin\Type\YesAndNoChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class responsible for providing columns, filters, actions for cart price rule list.
 */
final class CartRuleGridDefinitionFactory extends AbstractGridDefinitionFactory implements FilterableGridDefinitionFactoryInterface
{
    use BulkDeleteActionTrait;
    use DeleteActionTrait;

    public const GRID_ID = 'cart_rule';

    /**
     * @var string
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
     * {@inheritdoc}
     */
    public function getFilterId(): string
    {
        return self::GRID_ID;
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
        return $this->trans('Cart rules', [], 'Admin.Catalog.Feature');
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
                        'bulk_field' => 'id_cart_rule',
                    ])
            )
            ->add(
                (new DataColumn('id_cart_rule'))
                    ->setName($this->trans('ID', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'id_cart_rule',
                    ])
            )
            ->add(
                (new DataColumn('name'))
                    ->setName($this->trans('Name', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'name',
                    ])
            )
            ->add(
                (new DataColumn('priority'))
                    ->setName($this->trans('Priority', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'priority',
                    ])
            )
            ->add(
                (new DataColumn('code'))
                    ->setName($this->trans('Code', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'code',
                    ])
            )
            ->add(
                (new DataColumn('quantity'))
                    ->setName($this->trans('Quantity', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'quantity',
                    ])
            )
            ->add(
                (new DateTimeColumn('date_to'))
                    ->setName($this->trans('Expiration date', [], 'Admin.Catalog.Feature'))
                    ->setOptions([
                        'format' => $this->contextDateFormat,
                        'field' => 'date_to',
                    ])
            )
            ->add(
                (new ToggleColumn('active'))
                    ->setName($this->trans('Status', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'active',
                        'primary_field' => 'id_cart_rule',
                        'route' => 'admin_cart_rule_toggle_status',
                        'route_param_name' => 'cartRuleId',
                    ])
            )
            ->add(
                (new ActionColumn('actions'))
                    ->setName($this->trans('Actions', [], 'Admin.Global'))
                    ->setOptions([
                        'actions' => (new RowActionCollection())
                            ->add(
                                $this->buildDeleteAction(
                                    'admin_cart_rules_delete',
                                    'cartRuleId',
                                    'id_cart_rule',
                                    Request::METHOD_DELETE
                                )
                            ),
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
                (new Filter('id_cart_rule', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => [
                            'placeholder' => $this->trans('ID', [], 'Admin.Global'),
                        ],
                    ])
                    ->setAssociatedColumn('id_cart_rule')
            )
            ->add(
                (new Filter('name', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => [
                            'placeholder' => $this->trans('Name', [], 'Admin.Global'),
                        ],
                    ])
                    ->setAssociatedColumn('name')
            )
            ->add(
                (new Filter('priority', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => [
                            'placeholder' => $this->trans('Priority', [], 'Admin.Global'),
                        ],
                    ])
                    ->setAssociatedColumn('priority')
            )
            ->add(
                (new Filter('code', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => [
                            'placeholder' => $this->trans('Code', [], 'Admin.Global'),
                        ],
                    ])
                    ->setAssociatedColumn('code')
            )
            ->add(
                (new Filter('quantity', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => [
                            'placeholder' => $this->trans('Quantity', [], 'Admin.Global'),
                        ],
                    ])
                    ->setAssociatedColumn('quantity')
            )
            ->add(
                (new Filter('date_to', DateRangeType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => [
                            'placeholder' => $this->trans('Expiration date', [], 'Admin.Catalog.Feature'),
                        ],
                        'date_format' => 'YYYY/MM/DD',
                    ])
                    ->setAssociatedColumn('date_to')
            )
            ->add(
                (new Filter('active', YesAndNoChoiceType::class))
                    ->setAssociatedColumn('active')
            )
            ->add(
                (new Filter('actions', SearchAndResetType::class))
                    ->setAssociatedColumn('actions')
                    ->setTypeOptions([
                        'reset_route' => 'admin_common_reset_search_by_filter_id',
                        'reset_route_params' => [
                            'filterId' => self::GRID_ID,
                        ],
                        'redirect_route' => 'admin_cart_rules_index',
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
                (new SubmitBulkAction('enable_selection'))
                    ->setName($this->trans('Enable selection', [], 'Admin.Actions'))
                    ->setOptions([
                        'submit_route' => 'admin_cart_rules_bulk_enable_status',
                    ])
            )
            ->add(
                (new SubmitBulkAction('disable_selection'))
                    ->setName($this->trans('Disable selection', [], 'Admin.Actions'))
                    ->setOptions([
                        'submit_route' => 'admin_cart_rules_bulk_disable_status',
                    ])
            )
            ->add(
                $this->buildBulkDeleteAction('admin_cart_rules_bulk_delete')
            );
    }
}
