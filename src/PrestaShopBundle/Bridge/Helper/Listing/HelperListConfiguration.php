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

namespace PrestaShopBundle\Bridge\Helper\Listing;

use PrestaShopBundle\Bridge\AdminController\Action\ActionInterface;

/**
 * This object contains the configuration needed to generate a list using the helper list.
 *
 * @todo: is it worth adding getters/setters and making all properties private?
 */
class HelperListConfiguration
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $table;

    /**
     * @var string|null
     */
    public $listId;

    /**
     * @var string
     */
    public $objectModelClassName;

    /**
     * @var string
     */
    public $legacyControllerName;

    /**
     * @var string
     */
    public $identifier;

    /**
     * @var string
     */
    public $token;

    /**
     * @var string
     */
    public $select = '';

    /**
     * @var string
     */
    public $where = '';

    /**
     * @var string
     */
    public $join = '';

    /**
     * @var string
     */
    public $group = '';

    /**
     * @var string
     */
    public $orderBy = '';

    /**
     * @var string
     */
    public $defaultOrderBy;

    /**
     * @var string
     */
    public $orderWay = '';

    /**
     * @var string
     */
    public $defaultOrderWay = 'ASC';

    /**
     * @var string
     */
    public $having = '';

    /**
     * @var string
     */
    public $filterHaving = '';

    /**
     * @var string
     */
    public $filter = '';

    /**
     * @var bool
     */
    public $isJoinLanguageTableAuto = false;

    /**
     * @var int
     */
    public $defaultPagination = 50;

    /**
     * @var array
     */
    public $pagination = [20, 50, 100, 300, 1000];

    /**
     * @var string
     */
    public $shopLinkType = '';

    /**
     * @var int
     */
    public $multishopContext;

    /**
     * @var bool
     */
    public $deleted = false;

    /**
     * @var array
     */
    public $fieldsList = [];

    /**
     * @var string
     */
    public $listsql;

    /**
     * @var array
     */
    public $list;

    /**
     * @var int
     */
    public $listTotal;

    /**
     * @var string
     */
    public $listError;

    /**
     * @var bool
     */
    public $explicitSelect = false;

    /**
     * @var bool Use SQL_CALC_FOUND_ROWS / FOUND_ROWS to count the number of records
     */
    public $useFoundRows = true;

    /**
     * @var array
     */
    public $deleteLinksVariableTemplate = [];

    /**
     * @var array List of available actions for each list row - default actions are view, edit, delete, duplicate
     */
    public $actionsAvailable = [
        ActionInterface::AVAILABLE_ACTION_VIEW,
        ActionInterface::AVAILABLE_ACTION_EDIT,
        ActionInterface::AVAILABLE_ACTION_DUPLICATE,
        ActionInterface::AVAILABLE_ACTION_DELETE,
    ];

    /**
     * @var string
     */
    public $positionIdentifier;

    /**
     * @var bool
     */
    public $bootstrap;

    /**
     * @var string|null
     */
    public $legacyCurrentIndex;

    /**
     * @var array<string, array<string, mixed>>
     */
    private $toolbarActions = [];

    /**
     * @var string[]
     */
    private $rowActions = [];

    /**
     * @var array<string, array<string, mixed>>
     */
    private $bulkActions = [];

    /**
     * @param string $label
     * @param array $config
     *
     * @return $this
     */
    public function addToolbarAction(string $label, array $config): self
    {
        $this->toolbarActions[$label] = $config;

        return $this;
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function getToolbarActions(): array
    {
        return $this->toolbarActions;
    }

    public function addRowAction(string $action): self
    {
        $this->rowActions[] = $action;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getRowActions(): array
    {
        return $this->rowActions;
    }

    public function addBulkAction(string $label, array $config): self
    {
        $this->bulkActions[$label] = $config;

        return $this;
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function getBulkActions(): array
    {
        return $this->bulkActions;
    }
}
