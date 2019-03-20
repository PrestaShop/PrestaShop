<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory;


use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ToggleColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;

/**
 * Class responsible for providing columns, filters, actions for cms page list.
 */
class CmsPageDefinitionFactory extends AbstractGridDefinitionFactory
{
    /**
     * {@inheritdoc}
     */
    protected function getId()
    {
        return 'cms_page';
    }

    /**
     * {@inheritdoc}
     */
    protected function getName()
    {
        return 'test';
        // TODO: Implement getName() method.
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        return (new ColumnCollection())
            ->add((new BulkActionColumn('bulk'))
                ->setOptions([
                    'bulk_field' => 'id_cms',
                ])
            )
            ->add((new DataColumn('id_cms'))
                ->setName($this->trans('ID', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'id_cms',
                ])
            )
            ->add((new DataColumn('link_rewrite'))
                ->setName($this->trans('URL', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'link_rewrite',
                ])
            )
            ->add((new DataColumn('meta_title'))
                ->setName($this->trans('Title', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'meta_title',
                    'sortable' => false,
                ])
            )
            ->add((new DataColumn('head_seo_title'))
                ->setName($this->trans('Meta title', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'head_seo_title',
                ])
            )
            ->add((new DataColumn('position'))
                ->setName($this->trans('Position', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'meta_title',
                ])
            )
            ->add((new ToggleColumn('active'))
                ->setName($this->trans('Displayed', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'active',
                    'route' => 'admin_cms_pages_toggle_cms_category', //todo: route
                    'primary_field' => 'id_cms',
                    'route_param_name' => 'cmsCategoryId',
                ])
            )
        ;
    }
}
