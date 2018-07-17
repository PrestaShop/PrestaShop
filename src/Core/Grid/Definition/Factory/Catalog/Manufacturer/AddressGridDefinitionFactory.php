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

use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnFilterOption;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\AbstractGridDefinitionFactory;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetFormType;

final class AddressGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    /**
     * {@inheritdoc}
     */
    protected function getId()
    {
        return 'manufacturer_addresses';
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
                    'align' => 'center',
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
            ->add((new DataColumn('id_country'))
                ->setName($this->trans('Country', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'country_name',
                ])
            )
            ->add((new ActionColumn('actions'))
                ->setName($this->trans('Actions', [], 'Admin.Global'))
                ->setOptions([
                    'filter' => new ColumnFilterOption(SearchAndResetFormType::class, [
                        'attr' => [
                            'data-url' => '',
                            'data-redirect' => '',
                        ],
                    ]),
                ])
            )
        ;
    }
}
