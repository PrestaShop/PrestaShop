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

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;

/**
 * Class EmployeeGridDefinitionFactory creates grid definition for Employee data.
 */
final class EmployeeGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    /**
     * {@inheritdoc}
     */
    protected function getId()
    {
        return 'employee';
    }

    /**
     * {@inheritdoc}
     */
    protected function getName()
    {
        return $this->trans('Employees', [], 'Admin.Advparameters.Feature');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        return (new ColumnCollection())
            ->add((new DataColumn('id_employee'))
                ->setName($this->trans('ID', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'id_employee',
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
            ->add((new DataColumn('email'))
                ->setName($this->trans('Email address', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'email',
                ])
            )
            ->add((new DataColumn('profile_name'))
                ->setName($this->trans('Profile', [], 'Admin.Advparameters.Feature'))
                ->setOptions([
                    'field' => 'profile_name',
                ])
            )
            ->add((new DataColumn('active'))
                ->setName($this->trans('Active', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'active',
                ])
            )
        ;
    }
}
