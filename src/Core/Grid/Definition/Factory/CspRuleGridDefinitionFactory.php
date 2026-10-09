<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\Csp\CspSurfaceResolver;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\Type\SubmitBulkAction;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\ModalOptions;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\SubmitRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\SimpleGridAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BooleanColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DateTimeColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\RequestStack;

/** Builds the grid definition for the CSP allow-list (the curated csp_rule rows), with the Remove action. */
final class CspRuleGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    public const GRID_ID = 'csp_rule';

    public function __construct(
        HookDispatcherInterface $hookDispatcher,
        private readonly RequestStack $requestStack,
    ) {
        parent::__construct($hookDispatcher);
    }

    /** The back office is a single global surface; the grid shows it when ?context=admin. */
    private function isAdminContext(): bool
    {
        return CspSurfaceResolver::isAdminRequest($this->requestStack);
    }

    /** Curation links keep the current surface (?context=admin) so a back-office action stays in the back office. */
    private function contextRouteParams(): array
    {
        return $this->isAdminContext() ? ['context' => 'admin'] : [];
    }

    protected function getId(): string
    {
        return self::GRID_ID;
    }

    protected function getName(): string
    {
        return $this->trans('Allowed sources', [], 'Admin.Advparameters.Feature');
    }

    protected function getColumns(): ColumnCollection
    {
        $columns = (new ColumnCollection())
            ->add(
                (new BulkActionColumn('bulk_action'))
                    ->setOptions(['bulk_field' => 'id_csp_rule'])
            )
            ->add(
                (new DataColumn('directive'))
                    ->setName($this->trans('Directive', [], 'Admin.Advparameters.Feature'))
                    ->setOptions(['field' => 'directive'])
            )
            ->add(
                (new DataColumn('source'))
                    ->setName($this->trans('Allowed source', [], 'Admin.Advparameters.Feature'))
                    ->setOptions(['field' => 'source'])
            );

        // The back office is one global surface, so the Shop column is only meaningful on the storefront.
        if (!$this->isAdminContext()) {
            $columns->add(
                (new DataColumn('shop_name'))
                    ->setName($this->trans('Shop', [], 'Admin.Global'))
                    ->setOptions(['field' => 'shop_name'])
            );
        }

        return $columns
            ->add(
                (new BooleanColumn('is_weakening'))
                    ->setName($this->trans('Weakens policy', [], 'Admin.Advparameters.Feature'))
                    ->setOptions([
                        'field' => 'is_weakening',
                        'true_name' => $this->trans('Yes', [], 'Admin.Global'),
                        'false_name' => $this->trans('No', [], 'Admin.Global'),
                        'clickable' => false,
                    ])
            )
            ->add(
                (new DateTimeColumn('date_add'))
                    ->setName($this->trans('Added', [], 'Admin.Advparameters.Feature'))
                    ->setOptions(['field' => 'date_add'])
            )
            ->add(
                (new ActionColumn('actions'))
                    ->setName($this->trans('Actions', [], 'Admin.Global'))
                    ->setOptions([
                        'actions' => (new RowActionCollection())
                            ->add(
                                (new SubmitRowAction('remove'))
                                    ->setIcon('close')
                                    ->setName($this->trans('Remove', [], 'Admin.Actions'))
                                    ->setOptions([
                                        'method' => 'POST',
                                        'route' => 'admin_security_csp_revoke',
                                        'route_param_name' => 'cspRuleId',
                                        'route_param_field' => 'id_csp_rule',
                                        'extra_route_params' => $this->contextRouteParams(),
                                        'confirm_message' => $this->trans('Remove this source from the allow-list?', [], 'Admin.Advparameters.Feature'),
                                        'modal_options' => new ModalOptions([
                                            'title' => $this->trans('Remove source', [], 'Admin.Advparameters.Feature'),
                                            'confirm_button_label' => $this->trans('Remove', [], 'Admin.Actions'),
                                            'close_button_label' => $this->trans('Cancel', [], 'Admin.Actions'),
                                            'confirm_button_class' => 'btn-danger',
                                        ]),
                                    ])
                            ),
                    ])
            );
    }

    protected function getFilters()
    {
        return (new FilterCollection())
            ->add(
                (new Filter('directive', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => ['placeholder' => $this->trans('Search directive', [], 'Admin.Actions')],
                    ])
                    ->setAssociatedColumn('directive')
            )
            ->add(
                (new Filter('source', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => ['placeholder' => $this->trans('Search source', [], 'Admin.Actions')],
                    ])
                    ->setAssociatedColumn('source')
            )
            ->add(
                (new Filter('actions', SearchAndResetType::class))
                    ->setTypeOptions([
                        'reset_route' => 'admin_common_reset_search_by_filter_id',
                        'reset_route_params' => [
                            'filterId' => self::GRID_ID,
                        ],
                        'redirect_route' => 'admin_security_csp_index',
                        'redirect_route_params' => $this->contextRouteParams(),
                    ])
                    ->setAssociatedColumn('actions')
            );
    }

    protected function getGridActions()
    {
        return (new GridActionCollection())
            ->add(
                (new SimpleGridAction('common_refresh_list'))
                    ->setName($this->trans('Refresh list', [], 'Admin.Advparameters.Feature'))
                    ->setIcon('refresh')
            );
    }

    protected function getBulkActions()
    {
        return (new BulkActionCollection())
            ->add(
                (new SubmitBulkAction('remove_selection'))
                    ->setName($this->trans('Remove selected', [], 'Admin.Actions'))
                    ->setOptions([
                        // The bulk modal posts to a bare route (params are dropped), so the back office
                        // uses a context-carrying route; otherwise the remove would run on the storefront surface.
                        'submit_route' => $this->isAdminContext() ? 'admin_security_csp_bulk_revoke_admin' : 'admin_security_csp_bulk_revoke',
                        'confirm_message' => $this->trans('Remove the selected sources from the allow-list?', [], 'Admin.Advparameters.Feature'),
                        'modal_options' => new ModalOptions([
                            'title' => $this->trans('Remove selection', [], 'Admin.Advparameters.Feature'),
                            'confirm_button_label' => $this->trans('Remove', [], 'Admin.Actions'),
                            'confirm_button_class' => 'btn-danger',
                        ]),
                    ])
            );
    }
}
