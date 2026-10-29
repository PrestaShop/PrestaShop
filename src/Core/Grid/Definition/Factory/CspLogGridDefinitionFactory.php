<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\Csp\CspSurfaceResolver;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\ModalOptions;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\AccessibilityChecker\CspLogAllowAccessibilityChecker;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\AccessibilityChecker\CspLogWeakeningAllowAccessibilityChecker;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\SubmitRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\SimpleGridAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BooleanColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DateTimeColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\RequestStack;

/** Builds the grid definition for the collected CSP violations (one row per source per page), with the Allow action. */
final class CspLogGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    public const GRID_ID = 'csp_log';

    public function __construct(
        HookDispatcherInterface $hookDispatcher,
        private readonly CspLogAllowAccessibilityChecker $allowAccessibilityChecker,
        private readonly CspLogWeakeningAllowAccessibilityChecker $weakeningAllowAccessibilityChecker,
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
        return $this->trans('Reported violations', [], 'Admin.Advparameters.Feature');
    }

    protected function getColumns(): ColumnCollection
    {
        $columns = (new ColumnCollection())
            ->add(
                (new DataColumn('directive'))
                    ->setName($this->trans('Directive', [], 'Admin.Advparameters.Feature'))
                    ->setOptions(['field' => 'directive'])
            )
            ->add(
                (new DataColumn('source'))
                    ->setName($this->trans('Blocked source', [], 'Admin.Advparameters.Feature'))
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
                (new DataColumn('document_uri'))
                    ->setName($this->trans('Page', [], 'Admin.Advparameters.Feature'))
                    ->setOptions(['field' => 'document_uri'])
            )
            // Inline violations only report the keyword ('unsafe-inline'); the sample and location
            // identify which inline block triggered it.
            ->add(
                (new DataColumn('sample'))
                    ->setName($this->trans('Blocked code', [], 'Admin.Advparameters.Feature'))
                    ->setOptions(['field' => 'sample'])
            )
            ->add(
                (new DataColumn('source_location'))
                    ->setName($this->trans('Location', [], 'Admin.Advparameters.Feature'))
                    ->setOptions(['field' => 'source_location'])
            )
            ->add(
                (new DataColumn('hits'))
                    ->setName($this->trans('Reports', [], 'Admin.Advparameters.Feature'))
                    ->setOptions(['field' => 'hits'])
            )
            ->add(
                (new DateTimeColumn('date_add'))
                    ->setName($this->trans('First seen', [], 'Admin.Advparameters.Feature'))
                    ->setOptions(['field' => 'date_add'])
            )
            ->add(
                (new ActionColumn('actions'))
                    ->setName($this->trans('Actions', [], 'Admin.Global'))
                    ->setOptions([
                        'actions' => (new RowActionCollection())
                            ->add(
                                (new SubmitRowAction('allow'))
                                    ->setIcon('check')
                                    ->setName($this->trans('Allow', [], 'Admin.Advparameters.Feature'))
                                    ->setOptions([
                                        'method' => 'POST',
                                        'route' => 'admin_security_csp_allow',
                                        'route_param_name' => 'cspLogId',
                                        'route_param_field' => 'id_csp_log',
                                        'extra_route_params' => $this->contextRouteParams(),
                                        'accessibility_checker' => $this->allowAccessibilityChecker,
                                        'confirm_message' => $this->trans('Add this source to your allow-list?', [], 'Admin.Advparameters.Feature'),
                                        'modal_options' => new ModalOptions([
                                            'title' => $this->trans('Allow source', [], 'Admin.Advparameters.Feature'),
                                            'confirm_button_label' => $this->trans('Allow', [], 'Admin.Advparameters.Feature'),
                                            'close_button_label' => $this->trans('Cancel', [], 'Admin.Actions'),
                                            'confirm_button_class' => 'btn-primary',
                                        ]),
                                    ])
                            )
                            ->add(
                                // Same "Allow" icon as the safe action so the column reads consistently; the
                                // risk is carried by the "Weakens policy" column and a danger-styled confirm modal.
                                (new SubmitRowAction('allow_weakening'))
                                    ->setIcon('check')
                                    ->setName($this->trans('Allow (weakens policy)', [], 'Admin.Advparameters.Feature'))
                                    ->setOptions([
                                        'method' => 'POST',
                                        'route' => 'admin_security_csp_allow',
                                        'route_param_name' => 'cspLogId',
                                        'route_param_field' => 'id_csp_log',
                                        'extra_route_params' => $this->contextRouteParams(),
                                        'accessibility_checker' => $this->weakeningAllowAccessibilityChecker,
                                        'confirm_message' => $this->trans('This source weakens the Content Security Policy for the whole shop. Allow it anyway?', [], 'Admin.Advparameters.Feature'),
                                        'modal_options' => new ModalOptions([
                                            'title' => $this->trans('Allow a weakening source', [], 'Admin.Advparameters.Feature'),
                                            'confirm_button_label' => $this->trans('Allow', [], 'Admin.Advparameters.Feature'),
                                            'close_button_label' => $this->trans('Cancel', [], 'Admin.Actions'),
                                            'confirm_button_class' => 'btn-warning',
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
                        // Keep the current surface so Reset stays on the back-office tab.
                        'redirect_route_params' => $this->contextRouteParams(),
                    ])
                    ->setAssociatedColumn('actions')
            );
    }

    protected function getGridActions()
    {
        // Only "Refresh list": a curation surface has no use for the developer "Show SQL query" /
        // "Export to SQL Manager" actions, and this matches the allow-list grid.
        return (new GridActionCollection())
            ->add(
                (new SimpleGridAction('common_refresh_list'))
                    ->setName($this->trans('Refresh list', [], 'Admin.Advparameters.Feature'))
                    ->setIcon('refresh')
            );
    }
}
