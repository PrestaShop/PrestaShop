<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\Domain\TaxRule\TaxRuleSettings;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\SimpleGridAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollectionInterface;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Service\Attribute\Required;

/**
 * Defines tax rules grid
 */
class TaxRuleGridDefinitionFactory extends AbstractFilterableGridDefinitionFactory
{
    use BulkDeleteActionTrait;
    use DeleteActionTrait;

    public const GRID_ID = 'tax_rules';

    private RequestStack $requestStack;

    #[Required]
    public function setRequestStack(RequestStack $requestStack): void
    {
        $this->requestStack = $requestStack;
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
        return $this->trans('Tax rules', [], 'Admin.Navigation.Menu');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        return (new ColumnCollection())
            ->add(
                (new BulkActionColumn('bulk'))
                    ->setOptions([
                        'bulk_field' => 'id_tax_rule',
                    ])
            )
            ->add(
                (new DataColumn('country'))
                    ->setName($this->trans('Country', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'country_name',
                    ])
            )
            ->add(
                (new DataColumn('state'))
                    ->setName($this->trans('State', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'state_name',
                    ])
            )
            ->add(
                (new DataColumn('zipcode'))
                    ->setName($this->trans('Zip/Postal code', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'zipcode',
                    ])
            )
            ->add(
                (new DataColumn('behavior'))
                    ->setName($this->trans('Behavior', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'behavior',
                    ])
            )
            ->add(
                (new DataColumn('rate'))
                    ->setName($this->trans('Tax', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'rate',
                    ])
            )
            ->add(
                (new DataColumn('description'))
                    ->setName($this->trans('Description', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'description',
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
        $taxRulesGroupId = $this->getTaxRulesGroupIdFromRequest();

        return (new FilterCollection())
            ->add(
                (new Filter('country', TextType::class))
                    ->setAssociatedColumn('country')
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => [
                            'placeholder' => $this->trans('Search country', [], 'Admin.Actions'),
                        ],
                    ])
            )
            ->add(
                (new Filter('state', TextType::class))
                    ->setAssociatedColumn('state')
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => [
                            'placeholder' => $this->trans('Search state', [], 'Admin.Actions'),
                        ],
                    ])
            )
            ->add(
                (new Filter('zipcode', TextType::class))
                    ->setAssociatedColumn('zipcode')
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => [
                            'placeholder' => $this->trans('Search zip code', [], 'Admin.Actions'),
                        ],
                    ])
            )
            ->add(
                (new Filter('behavior', ChoiceType::class))
                    ->setAssociatedColumn('behavior')
                    ->setTypeOptions([
                        'required' => false,
                        'placeholder' => $this->trans('All', [], 'Admin.Global'),
                        'choices' => [
                            $this->trans('This tax only', [], 'Admin.International.Feature') => TaxRuleSettings::BEHAVIOR_TAX_ONLY,
                            $this->trans('Combine', [], 'Admin.International.Feature') => TaxRuleSettings::BEHAVIOR_COMBINE,
                            $this->trans('One after another', [], 'Admin.International.Feature') => TaxRuleSettings::BEHAVIOR_ONE_AFTER_ANOTHER,
                        ],
                    ])
            )
            ->add(
                (new Filter('rate', TextType::class))
                    ->setAssociatedColumn('rate')
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => [
                            'placeholder' => $this->trans('Search rate', [], 'Admin.Actions'),
                        ],
                    ])
            )
            ->add(
                (new Filter('description', TextType::class))
                    ->setAssociatedColumn('description')
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => [
                            'placeholder' => $this->trans('Search description', [], 'Admin.Actions'),
                        ],
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
                        'redirect_route' => 'admin_tax_rules_groups_edit',
                        'redirect_route_params' => [
                            'taxRulesGroupId' => $taxRulesGroupId,
                        ],
                    ])
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function getRowActions()
    {
        return (new RowActionCollection())
            ->add(
                (new LinkRowAction('edit'))
                    ->setName($this->trans('Edit', [], 'Admin.Actions'))
                    ->setIcon('edit')
                    ->setOptions([
                        'route' => 'admin_tax_rules_edit',
                        'route_param_name' => 'taxRuleId',
                        'route_param_field' => 'id_tax_rule',
                        'extra_route_params' => [
                            'taxRulesGroupId' => 'id_tax_rules_group',
                        ],
                    ])
            )
            ->add(
                $this->buildDeleteAction(
                    'admin_tax_rules_delete',
                    'taxRuleId',
                    'id_tax_rule',
                    Request::METHOD_DELETE,
                    [
                        'taxRulesGroupId' => 'id_tax_rules_group',
                    ]
                )
            );
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    protected function getBulkActions()
    {
        return (new BulkActionCollection())
            ->add(
                $this->buildBulkDeleteAction('admin_tax_rules_bulk_delete')
            );
    }

    private function getTaxRulesGroupIdFromRequest(): int
    {
        $request = $this->requestStack->getCurrentRequest();

        if (null === $request) {
            return 0;
        }

        return (int) $request->attributes->get('taxRulesGroupId', 0);
    }
}
