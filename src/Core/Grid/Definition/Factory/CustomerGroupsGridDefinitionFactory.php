<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CustomerGroupsGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    public const GRID_ID = 'customer_groups';

    /**
     * {@inheritDoc}
     */
    protected function getId()
    {
        return self::GRID_ID;
    }

    /**
     * {@inheritDoc}
     */
    protected function getName()
    {
        return $this->trans('Customer groups', [], 'Admin.Navigation.Menu');
    }

    /**
     * {@inheritDoc}
     */
    protected function getColumns()
    {
        return (new ColumnCollection())
            ->add(
                (new BulkActionColumn('title_bulk'))
                    ->setOptions([
                        'bulk_field' => 'id_group',
                    ])
            )
            ->add(
                (new DataColumn('id_group'))
                    ->setName($this->trans('ID', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'id_group',
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
                (new DataColumn('reduction'))
                    ->setName($this->trans('Discount (%)', [], 'Admin.Shopparameters.Feature'))
                    ->setOptions([
                        'field' => 'reduction',
                    ])
            )
            ->add(
                (new DataColumn('members'))
                    ->setName($this->trans('Members', [], 'Admin.Shopparameters.Feature'))
                    ->setOptions([
                        'field' => 'members',
                    ])
            )
            ->add(
                (new DataColumn('show_prices'))
                    ->setName($this->trans('Show prices', [], 'Admin.Shopparameters.Feature'))
                    ->setOptions([
                        'field' => 'show_prices',
                    ])
            )
            ->add(
                (new ActionColumn('actions'))
                    ->setName($this->trans('Actions', [], 'Admin.Global'))
                    ->setOptions([
                        'actions' => (new RowActionCollection())
                            ->add(
                                (new LinkRowAction('edit'))
                                    ->setName($this->trans('Edit', [], 'Admin.Actions'))
                                    ->setIcon('edit')
                                    ->setOptions([
                                        'route' => 'admin_customer_groups_edit',
                                        'route_param_name' => 'groupId',
                                        'route_param_field' => 'id_group',
                                        'clickable_row' => true,
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
        return (new FilterCollection())
            ->add(
                (new Filter('id_group', NumberType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => [
                            'placeholder' => $this->translator->trans('Search ID', [], 'Admin.Actions'),
                        ],
                    ])
                    ->setAssociatedColumn('id_group')
            )
            ->add(
                (new Filter('name', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => [
                            'placeholder' => $this->translator->trans('Search name', [], 'Admin.Global'),
                        ],
                    ])
                    ->setAssociatedColumn('name')
            )
            ->add(
                (new Filter('reduction', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => [
                            'placeholder' => $this->translator->trans('Search', [], 'Admin.Global'),
                        ],
                    ])
                    ->setAssociatedColumn('reduction')
            )
            ->add(
                (new Filter('show_prices', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => [
                            'placeholder' => $this->translator->trans('Search', [], 'Admin.Global'),
                        ],
                    ])
                    ->setAssociatedColumn('show_prices')
            )
            ->add(
                (new Filter('actions', SearchAndResetType::class))
                    ->setTypeOptions([
                        'reset_route' => 'admin_common_reset_search_by_filter_id',
                        'reset_route_params' => [
                            'filterId' => self::GRID_ID,
                        ],
                        'redirect_route' => 'admin_customer_groups_index',
                    ])
                    ->setAssociatedColumn('actions')
            )
        ;
    }
}
