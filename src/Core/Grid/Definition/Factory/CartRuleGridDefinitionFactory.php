<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DateTimeColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;

/**
 * Class responsible for providing columns, filters, actions for cart price rule list.
 */
final class CartRuleGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    const GRID_ID = 'cart_rule';

    public function __construct(
        HookDispatcherInterface $hookDispatcher
    ) {
        parent::__construct($hookDispatcher);
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
        return $this->trans('Cart rules', [], 'Admin.Catalog.Feature');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        return (new ColumnCollection())
            ->add((new BulkActionColumn('bulk'))
            ->setOptions([
                'bulk_field' => 'id_cart_rule',
            ])
            )
            ->add((new DataColumn('id_cart_rule'))
            ->setName($this->trans('ID', [], 'Admin.Global'))
            ->setOptions([
                'field' => 'id_cart_rule',
            ])
            )
            ->add((new DataColumn('name'))
            ->setName($this->trans('Name', [], 'Admin.Global'))
            ->setOptions([
                'field' => 'name',
            ])
            )
            ->add((new DataColumn('priority'))
            ->setName($this->trans('Priority', [], 'Admin.Global'))
            ->setOptions([
                'field' => 'priority',
            ])
            )
            ->add((new DataColumn('code'))
            ->setName($this->trans('Code', [], 'Admin.Global'))
            ->setOptions([
                'field' => 'code',
            ])
            )
            ->add((new DataColumn('quantity'))
            ->setName($this->trans('Quantity', [], 'Admin.Catalog.Feature'))
            ->setOptions([
                'field' => 'quantity',
            ])
            )
            ->add((new DateTimeColumn('date_to'))
            ->setName($this->trans('Expiration date', [], 'Admin.Catalog.Feature'))
            ->setOptions([
                'format' => 'Y-m-d H:i',
                'field' => 'date_to',
            ])
            );
    }
}
