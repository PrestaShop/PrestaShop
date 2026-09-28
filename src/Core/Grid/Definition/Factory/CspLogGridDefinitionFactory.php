<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\SimpleGridAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DateTimeColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Builds the grid definition for the collected CSP violation log.
 *
 * In this phase the grid is read-only: it lists the distinct reported sources with their hit count
 * so the merchant can see the storefront's footprint. Row/bulk curation actions and the "Allowed?"
 * column are added in a later phase together with the rules.
 */
final class CspLogGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    public const GRID_ID = 'csp_log';

    protected function getId(): string
    {
        return self::GRID_ID;
    }

    protected function getName(): string
    {
        return $this->trans('Reported sources', [], 'Admin.Advparameters.Feature');
    }

    protected function getColumns(): ColumnCollection
    {
        return (new ColumnCollection())
            ->add(
                (new DataColumn('directive'))
                    ->setName($this->trans('Directive', [], 'Admin.Advparameters.Feature'))
                    ->setOptions(['field' => 'directive'])
            )
            ->add(
                (new DataColumn('source'))
                    ->setName($this->trans('Blocked source', [], 'Admin.Advparameters.Feature'))
                    ->setOptions(['field' => 'source'])
            )
            ->add(
                (new DataColumn('document_uri'))
                    ->setName($this->trans('Page', [], 'Admin.Advparameters.Feature'))
                    ->setOptions(['field' => 'document_uri'])
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
}
