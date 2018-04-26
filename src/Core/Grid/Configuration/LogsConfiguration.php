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

namespace PrestaShop\PrestaShop\Core\Grid\Configuration;

use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShopBundle\Service\Hook\HookDispatcher;

final class LogsConfiguration implements ConfigurationInterface
{
    /**
     * @var HookDispatcher
     */
    private $hookDispatcher;

    /**
     * @param string The Grid name.
     */
    private $name;

    public function __construct(HookDispatcher $hookDispatcher)
    {
        $this->hookDispatcher = $hookDispatcher;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getColumns()
    {
        $columns = ColumnCollection::createFromArray([
            ['name' => 'ID'],
            ['name' => 'Employee'],
            ['name' => 'Severity (1-4)'],
            ['name' => 'Message'],
            ['name' => 'Object type'],
            ['name' => 'Error code'],
            ['name' => 'Date', 'type' => 'date_interval']
        ]);

        $this->hookDispatcher->dispatchForParameters("change{$this->name}Columns", [
           'columns' => &$columns,
        ]);

        return $columns;
    }

    /**
     * {@inheritdoc}
     */
    public function enableSearch()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getActions()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getBulkActions()
    {
        return [];
    }
}
