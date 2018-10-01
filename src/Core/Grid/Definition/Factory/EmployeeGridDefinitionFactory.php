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
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ToggleColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

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
            ->add((new DataColumn('first_name'))
                ->setName($this->trans('First name', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'firstname',
                ])
            )
            ->add((new DataColumn('last_name'))
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
            ->add((new DataColumn('profile'))
                ->setName($this->trans('Profile', [], 'Admin.Advparameters.Feature'))
                ->setOptions([
                    'field' => 'profile_name',
                ])
            )
            ->add((new ToggleColumn('active'))
                ->setName($this->trans('Active', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'active',
                    'primary_field' => 'id_employee',
                    'route' => 'admin_employees_index',
                    'route_param_id' => 'employeeId',
                ])
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function getFilters()
    {
        return (new FilterCollection())
            ->add((new Filter('id_employee', NumberType::class))
                ->setTypeOptions([
                    'required' => false,
                ])
                ->setAssociatedColumn('id_employee')
            )
            ->add((new Filter('first_name', TextType::class))
                ->setTypeOptions([
                    'required' => false,
                ])
                ->setAssociatedColumn('first_name')
            )
            ->add((new Filter('last_name', TextType::class))
                ->setTypeOptions([
                    'required' => false,
                ])
                ->setAssociatedColumn('last_name')
            )
            ->add((new Filter('email', EmailType::class))
                ->setTypeOptions([
                    'required' => false,
                ])
                ->setAssociatedColumn('email')
            )
            ->add((new Filter('profile', TextType::class))
                ->setTypeOptions([
                    'required' => false,
                ])
                ->setAssociatedColumn('profile')
            )
            ->add((new Filter('active', TextType::class))
                ->setTypeOptions([
                    'required' => false,
                ])
                ->setAssociatedColumn('active')
            )
        ;
    }
}
