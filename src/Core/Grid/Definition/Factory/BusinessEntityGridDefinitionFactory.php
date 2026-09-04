<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Form\ChoiceProvider\BusinessEntityStatusChoiceProvider;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\SimpleGridAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BadgeColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class BusinessEntityGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    public const GRID_ID = 'business_entity';

    public function __construct(
        HookDispatcherInterface $hookDispatcher,
        private readonly ShopContext $shopContext,
        private readonly BusinessEntityStatusChoiceProvider $statusChoiceProvider,
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
        return $this->trans('Business entities', [], 'Admin.Navigation.Menu');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns(): ColumnCollection
    {
        $columns = (new ColumnCollection())
            ->add(
                (new DataColumn('id_business_entity'))
                    ->setName($this->trans('ID', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'id_business_entity',
                    ])
            )
            ->add(
                (new DataColumn('name'))
                    ->setName($this->trans('Company', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'name',
                    ])
            )
            ->add(
                (new DataColumn('legal_name'))
                    ->setName($this->trans('Legal name', [], 'Admin.Orderscustomers.Feature'))
                    ->setOptions([
                        'field' => 'legal_name',
                    ])
            );

        if ($this->shopContext->isMultiShopUsed()) {
            $columns->add(
                (new DataColumn('shop_name'))
                    ->setName($this->trans('Shop', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'shop_name',
                    ])
            );
        }

        return $columns
            ->add(
                (new BadgeColumn('customers_count'))
                    ->setName($this->trans('Customers', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'customers_count',
                        'alignment' => 'left',
                    ])
            )
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
                                (new LinkRowAction('view'))
                                    ->setName($this->trans('View details', [], 'Admin.Actions'))
                                    ->setIcon('visibility')
                                    ->setOptions([
                                        'route' => 'admin_business_entities_view',
                                        'route_param_name' => 'businessEntityId',
                                        'route_param_field' => 'id_business_entity',
                                        'clickable_row' => true,
                                    ])
                            )
                            ->add(
                                (new LinkRowAction('edit'))
                                    ->setName($this->trans('Edit', [], 'Admin.Actions'))
                                    ->setIcon('edit')
                                    ->setOptions([
                                        'route' => 'admin_business_entities_edit',
                                        'route_param_name' => 'businessEntityId',
                                        'route_param_field' => 'id_business_entity',
                                    ])
                            ),
                    ])
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function getFilters()
    {
        $filters = (new FilterCollection())
            ->add(
                (new Filter('id_business_entity', NumberType::class))
                    ->setTypeOptions([
                        'attr' => [
                            'placeholder' => $this->trans('Search ID', [], 'Admin.Actions'),
                        ],
                        'required' => false,
                    ])
                    ->setAssociatedColumn('id_business_entity')
            )
            ->add(
                (new Filter('name', TextType::class))
                    ->setTypeOptions([
                        'attr' => [
                            'placeholder' => $this->trans('Search name', [], 'Admin.Actions'),
                        ],
                        'required' => false,
                    ])
                    ->setAssociatedColumn('name')
            )
            ->add(
                (new Filter('legal_name', TextType::class))
                    ->setTypeOptions([
                        'attr' => [
                            'placeholder' => $this->trans('Search legal name', [], 'Admin.Actions'),
                        ],
                        'required' => false,
                    ])
                    ->setAssociatedColumn('legal_name')
            )
        ;

        if ($this->shopContext->isMultiShopUsed()) {
            $filters->add(
                (new Filter('shop_name', TextType::class))
                    ->setTypeOptions([
                        'attr' => [
                            'placeholder' => $this->trans('Search shop', [], 'Admin.Actions'),
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
                        'redirect_route' => 'admin_business_entities_list',
                    ])
                    ->setAssociatedColumn('actions')
            )
        ;
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
}
