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
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\LinkGridAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\SimpleGridAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DataColumn;
<<<<<<< HEAD
=======
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ToggleColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use PrestaShopBundle\Form\Admin\Type\YesAndNoChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
>>>>>>> af36b905e3 ( integrate delete and bulkDelete & bulk status actions into grid)

class StoreGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    use DeleteActionTrait;
    use BulkDeleteActionTrait;

    public const GRID_ID = 'store';

    protected function getId(): string
    {
        return self::GRID_ID;
    }

    protected function getName(): string
    {
        return $this->trans('Stores', [], 'Admin.Global');
    }

    protected function getColumns(): ColumnCollectionInterface
    {
        return (new ColumnCollection())
            ->add((new BulkActionColumn('bulk'))
            ->setOptions([
                'bulk_field' => 'id_store',
            ])
            )
            ->add((new DataColumn('id_store'))
            ->setName($this->trans('ID', [], 'Admin.Global'))
            ->setOptions([
                'field' => 'id_store',
            ])
            )
            ->add((new DataColumn('name'))
            ->setName($this->trans('Name', [], 'Admin.Global'))
            ->setOptions([
                'field' => 'name',
            ])
            )
            ->add((new DataColumn('address'))
            ->setName($this->trans('Address', [], 'Admin.Global'))
            ->setOptions([
                'field' => 'address',
            ])
            )
            ->add((new DataColumn('city'))
            ->setName($this->trans('City', [], 'Admin.Global'))
            ->setOptions([
                'field' => 'city',
            ])
            )
            ->add((new DataColumn('state'))
            ->setName($this->trans('State', [], 'Admin.Global'))
            ->setOptions([
                'field' => 'state',
            ])
            )
            ->add((new DataColumn('country'))
            ->setName($this->trans('Country', [], 'Admin.Global'))
            ->setOptions([
                'field' => 'country',
            ])
            )
            ->add((new DataColumn('phone'))
            ->setName($this->trans('Phone', [], 'Admin.Global'))
            ->setOptions([
                'field' => 'phone',
            ])
            )
            ->add((new DataColumn('fax'))
            ->setName($this->trans('Fax', [], 'Admin.Global'))
            ->setOptions([
                'field' => 'fax',
            ])
            )
            // @todo: make "active" a toggleColumn when toggle action is implemented
            ->add((new DataColumn('active'))
            ->setName($this->trans('Enabled', [], 'Admin.Global'))
            ->setOptions([
                'field' => 'active',
            ])
            )
            ->add((new ActionColumn('actions'))
            ->setName($this->trans('Actions', [], 'Admin.Global'))
<<<<<<< HEAD
                // @todo: uncomment when edit and delte actions are implemented
                //->setOptions([
                    //'actions' => (new RowActionCollection())
                        //->add((new LinkRowAction('edit'))
                            //->setName($this->trans('Edit', [], 'Admin.Actions'))
                            //->setIcon('edit')
                            //->setOptions([
                                //'route' => 'admin_stores_edit',
                                //'route_param_name' => 'storeId',
                                //'route_param_field' => 'id_store',
                            //])
                        //)
                        //->add(
                            //$this->buildDeleteAction(
                                //'admin_stores_delete',
                                //'storeId',
                                //'id_store',
                                //Request::METHOD_DELETE
                            //)
                        //),
                //])
=======
            ->setOptions([
                'actions' => (new RowActionCollection())
                    //@todo: uncomment when edit action is implemented
                    //->add((new LinkRowAction('edit'))
                    //->setName($this->trans('Edit', [], 'Admin.Actions'))
                    //->setIcon('edit')
                    //->setOptions([
                        //'route' => 'admin_stores_edit',
                        //'route_param_name' => 'storeId',
                        //'route_param_field' => 'id_store',
                    //])
                    //)
                    ->add(
                        $this->buildDeleteAction(
                            'admin_stores_delete',
                            'storeId',
                            'id_store',
                            Request::METHOD_DELETE
                        )
                    ),
            ])
>>>>>>> af36b905e3 ( integrate delete and bulkDelete & bulk status actions into grid)
            );
    }

    protected function getGridActions(): GridActionCollectionInterface
    {
        return (new GridActionCollection())
            ->add((new LinkGridAction('import'))
            ->setName($this->trans('Import', [], 'Admin.Actions'))
            ->setIcon('cloud_upload')
            ->setOptions([
                'route' => 'admin_import',
                'route_params' => [
                    'import_type' => 'stores',
                ],
            ])
            )
            ->add((new SimpleGridAction('common_refresh_list'))
            ->setName($this->trans('Refresh list', [], 'Admin.Advparameters.Feature'))
            ->setIcon('refresh')
            )
            ->add((new SimpleGridAction('common_show_query'))
            ->setName($this->trans('Show SQL query', [], 'Admin.Actions'))
            ->setIcon('code')
            )
            ->add((new SimpleGridAction('common_export_sql_manager'))
            ->setName($this->trans('Export to SQL Manager', [], 'Admin.Actions'))
            ->setIcon('storage')
            );
    }

<<<<<<< HEAD
//    /**
//     * {@inheritdoc}
//     */
//    protected function getFilters()
//    {
//        return (new FilterCollection())
//            ->add((new Filter('id_store', TextType::class))
//                ->setTypeOptions([
//                    'required' => false,
//                    'attr' => [
//                        'placeholder' => $this->trans('Search ID', [], 'Admin.Actions'),
//                    ],
//                ])
//                ->setAssociatedColumn('id_store')
//            )
//            ->add((new Filter('name', TextType::class))
//                ->setTypeOptions([
//                    'required' => false,
//                    'attr' => [
//                        'placeholder' => $this->trans('Search name', [], 'Admin.Actions'),
//                    ],
//                ])
//                ->setAssociatedColumn('name')
//            )
//            ->add(
//                (new Filter('active', YesAndNoChoiceType::class))
//                    ->setAssociatedColumn('active')
//            )
//            ->add((new Filter('actions', SearchAndResetType::class))
//                ->setAssociatedColumn('actions')
//                ->setTypeOptions([
//                    'reset_route' => 'admin_common_reset_search_by_filter_id',
//                    'reset_route_params' => [
//                        'filterId' => self::GRID_ID,
//                    ],
//                    'redirect_route' => 'admin_stores_index',
//                ])
//                ->setAssociatedColumn('actions')
//            );
//    }

//    /**
//     * {@inheritdoc}
//     */
//    protected function getBulkActions()
//    {
//        return (new BulkActionCollection())
//            ->add((new SubmitBulkAction('enable_selection'))
//                ->setName($this->trans('Enable selection', [], 'Admin.Actions'))
//                ->setOptions([
//                    'submit_route' => 'admin_stores_bulk_enable_status',
//                ])
//            )
//            ->add((new SubmitBulkAction('disable_selection'))
//                ->setName($this->trans('Disable selection', [], 'Admin.Actions'))
//                ->setOptions([
//                    'submit_route' => 'admin_stores_bulk_disable_status',
//                ])
//            )->add(
//                $this->buildBulkDeleteAction('admin_stores_bulk_delete')
//            );
//    }
=======
    protected function getBulkActions(): BulkActionCollectionInterface
    {
        return (new BulkActionCollection())
            ->add((new SubmitBulkAction('store_bulk_enable'))
            ->setName($this->trans('Enable selection', [], 'Admin.Actions'))
            ->setOptions([
                'submit_route' => 'admin_stores_bulk_enable',
            ])
            )
            ->add((new SubmitBulkAction('store_bulk_disable'))
            ->setName($this->trans('Disable selection', [], 'Admin.Actions'))
            ->setOptions([
                'submit_route' => 'admin_stores_bulk_disable',
            ])
            )
            ->add($this->buildBulkDeleteAction('admin_stores_bulk_delete'))
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function getFilters()
    {
        return (new FilterCollection())
            ->add((new Filter('id_store', TextType::class))
            ->setTypeOptions([
                'required' => false,
                'attr' => [
                    'placeholder' => $this->trans('Search ID', [], 'Admin.Actions'),
                ],
            ])
            ->setAssociatedColumn('id_store')
            )
            ->add((new Filter('name', TextType::class))
            ->setTypeOptions([
                'required' => false,
                'attr' => [
                    'placeholder' => $this->trans('Search name', [], 'Admin.Actions'),
                ],
            ])
            ->setAssociatedColumn('name')
            )
            ->add((new Filter('address', TextType::class))
            ->setTypeOptions([
                'required' => false,
                'attr' => [
                    'placeholder' => $this->trans('Search address', [], 'Admin.Actions'),
                ],
            ])
            ->setAssociatedColumn('address')
            )
            ->add((new Filter('city', TextType::class))
            ->setTypeOptions([
                'required' => false,
                'attr' => [
                    'placeholder' => $this->trans('Search city', [], 'Admin.Actions'),
                ],
            ])
            ->setAssociatedColumn('city')
            )
            ->add((new Filter('postcode', TextType::class))
            ->setTypeOptions([
                'required' => false,
                'attr' => [
                    'placeholder' => $this->trans('Search zip/postal code', [], 'Admin.Actions'),
                ],
            ])
            ->setAssociatedColumn('postcode')
            )
            ->add((new Filter('state', TextType::class))
            ->setTypeOptions([
                'required' => false,
                'attr' => [
                    'placeholder' => $this->trans('Search state', [], 'Admin.Actions'),
                ],
            ])
            ->setAssociatedColumn('state')
            )
            ->add((new Filter('country', TextType::class))
            ->setTypeOptions([
                'required' => false,
                'attr' => [
                    'placeholder' => $this->trans('Search country', [], 'Admin.Actions'),
                ],
            ])
            ->setAssociatedColumn('country')
            )
            ->add((new Filter('phone', TextType::class))
            ->setTypeOptions([
                'required' => false,
                'attr' => [
                    'placeholder' => $this->trans('Search phone', [], 'Admin.Actions'),
                ],
            ])
            ->setAssociatedColumn('phone')
            )
            ->add((new Filter('fax', TextType::class))
            ->setTypeOptions([
                'required' => false,
                'attr' => [
                    'placeholder' => $this->trans('Search fax', [], 'Admin.Actions'),
                ],
            ])
            ->setAssociatedColumn('fax')
            )
            ->add((new Filter('active', YesAndNoChoiceType::class))
            ->setAssociatedColumn('active')
            )
            ->add((new Filter('actions', SearchAndResetType::class))
            ->setAssociatedColumn('actions')
            ->setTypeOptions([
                'reset_route' => 'admin_common_reset_search_by_filter_id',
                'reset_route_params' => [
                    'filterId' => self::GRID_ID,
                ],
                'redirect_route' => 'admin_stores_index',
            ])
            ->setAssociatedColumn('actions')
            );
    }
>>>>>>> af36b905e3 ( integrate delete and bulkDelete & bulk status actions into grid)
}
