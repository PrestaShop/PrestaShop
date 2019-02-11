<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory\Catalog\Manufacturer;

use PrestaShop\PrestaShop\Core\Grid\Action\GridAction;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\SubmitRowAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnFilterOption;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\AbstractGridDefinitionFactory;
use PrestaShopBundle\Form\Admin\Type\CountryChoiceType;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetFormType;

/**
 * Class ManufacturerAddressGridDefinitionFactory is responsible for creating Manufacturers address grid definition.
 */
final class ManufacturerAddressGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    /**
     * @var string
     */
    private $searchResetUrl;

    /**
     * @var string
     */
    private $redirectUrl;

    /**
     * @param string $searchResetUrl
     * @param string $redirectUrl
     */
    public function __construct($searchResetUrl, $redirectUrl)
    {
        $this->searchResetUrl = $searchResetUrl;
        $this->redirectUrl = $redirectUrl;
    }

    /**
     * {@inheritdoc}
     */
    protected function getId()
    {
        return 'Manufacturer_addresses';
    }

    /**
     * {@inheritdoc}
     */
    protected function getName()
    {
        return $this->trans('Addresses', [], 'Admin.Catalog.Feature');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        return (new ColumnCollection())
            ->add((new BulkActionColumn('bulk'))
                ->setOptions([
                    'bulk_field' => 'id_address',
                ])
            )
            ->add((new DataColumn('id_address'))
                ->setName($this->trans('ID', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'id_address',
                ])
            )
            ->add((new DataColumn('name'))
                ->setName($this->trans('Brand', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'name',
                    'modifier' => function ($row) {
                        $row['name'] = $row['name'] ?: '--';

                        return $row;
                    },
                ])
            )
            ->add((new DataColumn('firstname'))
                ->setName($this->trans('First name', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'firstname',
                ])
            )
            ->add((new DataColumn('lastname'))
                ->setName($this->trans('Last name', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'lastname',
                ])
            )
            ->add((new DataColumn('postcode'))
                ->setName($this->trans('Zip/Postal code', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'postcode',
                ])
            )
            ->add((new DataColumn('city'))
                ->setName($this->trans('City', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'city',
                ])
            )
            ->add((new DataColumn('country'))
                ->setName($this->trans('Country', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'country_name',
                ])
            )
            ->add((new ActionColumn('actions'))
                ->setName($this->trans('Actions', [], 'Admin.Global'))
                ->setOptions([
                    'actions' => (new RowActionCollection())
                        ->add((new LinkRowAction('edit'))
                            ->setName($this->trans('Edit', [], 'Admin.Actions'))
                            ->setIcon('edit')
                            ->setOptions([
                                'route' => 'admin_manufacturers_addresses_edit',
                                'route_param_name' => 'manufacturerAddressId',
                                'route_param_field' => 'id_address',
                            ])
                        )
                        ->add((new SubmitRowAction('delete'))
                            ->setName($this->trans('Delete', [], 'Admin.Actions'))
                            ->setIcon('delete')
                            ->setOptions([
                                'method' => 'DELETE',
                                'route' => 'admin_manufacturers_addresses_delete',
                                'route_param_name' => 'manufacturerAddressId',
                                'route_param_field' => 'id_address',
                                'confirm_message' => $this->trans(
                                    'Delete selected item?',
                                    [],
                                    'Admin.Notifications.Warning'
                                ),
                            ])
                        ),
                ])
            )
//            ->add((new DataColumn('id_country'))
//                ->setName($this->trans('Country', [], 'Admin.Global'))
//                ->setOptions([
//                    'field' => 'country_name',
//                    'filter' => new ColumnFilterOption(CountryChoiceType::class),
//                ])
//            )
        ;
    }

//    /**
//     * {@inheritdoc}
//     */
//    protected function getGridActions()
//    {
//        return (new GridActionCollection())
//            ->add(new GridAction(
//                'common_refresh_list',
//                $this->trans('Refresh list', [], 'Admin.Advparameters.Feature'),
//                'refresh',
//                'simple'
//            ))
//            ->add(new GridAction(
//                'common_show_query',
//                $this->trans('Show SQL query', [], 'Admin.Actions'),
//                'code',
//                'simple'
//            ))
//            ->add(new GridAction(
//                'common_export_sql_manager',
//                $this->trans('Export to SQL Manager', [], 'Admin.Actions'),
//                'storage',
//                'simple'
//            ))
//        ;
//    }
}
