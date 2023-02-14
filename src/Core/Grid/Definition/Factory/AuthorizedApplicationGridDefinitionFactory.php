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

use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\AuthorizationServer\ApiAccessesStatesColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\LinkColumn;

/**
 * Class AuthorizedApplicationGridDefinitionFactory is responsible for creating new instance of Authorized Application grid definition.
 *
 * @experimental
 */
final class AuthorizedApplicationGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    use DeleteActionTrait;

    public const GRID_ID = 'authorized_application';

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
        return $this->trans('List of applications', [], 'Admin.Navigation.Menu');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns(): ColumnCollectionInterface
    {
        $columns = (new ColumnCollection())
            ->add(
                (new LinkColumn('name'))
                    ->setName($this->trans('Name', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'name',
                        'route' => 'admin_authorized_applications_view',
                        'route_param_name' => 'authorizedApplicationId',
                        'route_param_field' => 'id_authorized_application',
                    ])
            )
            ->add(
                (new ApiAccessesStatesColumn('api_accesses_states'))
                    ->setName($this->trans('API access state', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'apiAccesses',
                    ])
            )
            ->add((new ActionColumn('actions'))
            ->setName($this->trans('Actions', [], 'Admin.Global'))
            ->setOptions([
                'actions' => $this->getRowActions(),
            ])
            )
        ;

        return $columns;
    }

    /**
     * {@inheritdoc}
     */
    protected function getRowActions(): RowActionCollection
    {
        $rowActions = new RowActionCollection();
        $rowActions
            ->add((new LinkRowAction('edit'))
            ->setName($this->trans('Edit', [], 'Admin.Actions'))
            ->setIcon('edit')
            ->setOptions([
                'route' => 'admin_authorized_applications_edit',
                'route_param_name' => 'authorizedApplicationId',
                'route_param_field' => 'id_authorized_application',
            ])
            )
            ->add(
                (new LinkRowAction('view'))
                    ->setName($this->trans('View', [], 'Admin.Actions'))
                    ->setIcon('zoom_in')
                    ->setOptions([
                        'route' => 'admin_authorized_applications_view',
                        'route_param_name' => 'authorizedApplicationId',
                        'route_param_field' => 'id_authorized_application',
                        'clickable_row' => true,
                    ])
            )
            ->add(
                $this->buildDeleteAction(
                    'admin_authorized_applications_delete',
                    'authorizedApplicationId',
                    'id_authorized_application'
                )
            );

        return $rowActions;
    }
}
