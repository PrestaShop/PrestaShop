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

use AdminController;
use HelperList;
use PrestaShopBundle\Bridge\AdminController\ControllerConfiguration;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This object contains the configuration needed to generate a list using the legacy HelperList class.
 */
class HelperListConfiguration
{
    /**
     * Sql SELECT clause
     *
     * @see AdminController::$_select
     *
     * @var string
     */
    public $select = '';

    /**
     * Sql WHERE clause
     *
     * @see AdminController::$_where
     *
     * @var string
     */
    public $where = '';

    /**
     * Sql JOIN clause
     *
     * @see AdminController::$_join
     *
     * @var string
     */
    public $join = '';

    /**
     * Sql GROUP clause
     *
     * @see AdminController::$_group
     *
     * @var string
     */
    public $group = '';

    /**
     * Sql ORDER BY clause
     *
     * @see AdminController::$_orderBy
     *
     * @var string
     */
    public $orderBy = '';

    /**
     * Sql value for ORDER BY clause argument e.g. ASC|DESC
     *
     * @see AdminController::$_orderWay
     *
     * @var string
     */
    public $orderWay = '';

    /**
     * Sql HAVING clause
     *
     * @see AdminController::$_having
     *
     * @var string
     */
    public $having = '';

    /**
     * Sql HAVING clause for filters
     *
     * @see AdminController::$_filterHaving
     *
     * @var string
     */
    public $filterHaving;

    /**
     * Contains various sql clauses for filtering the list
     *
     * @see AdminController::$filter
     *
     * @var string
     */
    public $filter = '';

    /**
     * The definition of list fields
     *
     * @see HelperList::$fields_list
     *
     * @var array<string, array<string, mixed>>
     *
     * @see self::setFieldsList() for defined array structure
     */
    public $fieldsList = [];

    /**
     * List record rows
     *
     * @see HelperList::$_list
     *
     * @var array<int, array<string, mixed>>|null
     */
    public $list;

    /**
     * Total count of records found. Used for pagination.
     *
     * @see HelperList::$listTotal
     *
     * @var int
     */
    public $listTotal;

    /**
     * @see ControllerConfiguration::$tabId
     *
     * @var int
     */
    private $tabId;

    /**
     * @see ControllerConfiguration::$tableName
     * @see HelperList::$table
     *
     * @var string
     */
    private $tableName;

    /**
     * Identifier of the list.
     * Based on this value the submit request key is built to allow identifying which list is performing certain action.
     * Most of the time it matches table name, but custom value can be provided as well.
     *
     * @see HelperList::$list_id
     *
     * @var string
     */
    private $listId;

    /**
     * @see ControllerConfiguration::$objectModelClassName
     *
     * @var string
     */
    private $objectModelClassName;

    /**
     * @see ControllerConfiguration::$legacyControllerName
     * @see HelperList::$controller_name
     *
     * @var string
     */
    private $legacyControllerName;

    /**
     * Name of id field (e.g. id_feature)
     *
     * @see HelperList::$identifier
     *
     * @var string
     */
    private $identifierKey;

    /**
     * @see ControllerConfiguration::$token
     *
     * @var string
     */
    private $token;

    /**
     * Default value of sql ORDER BY clause
     *
     * @see HelperList::$_defaultOrderBy
     *
     * @var string
     */
    private $defaultOrderBy;

    /**
     * Default value for sql ORDER BY clause argument e.g. ASC|DESC
     *
     * @var string
     */
    private $defaultOrderWay = 'ASC';

    /**
     * Defines if language table should be joined when querying sql or not
     *
     * @see AdminController::$lang
     *
     * @var bool
     */
    private $autoJoinLanguageTable;

    /**
     * @see AdminController::$multishop_context
     *
     * @var int
     */
    private $multiShopContext;

    /**
     *  If set to true, then includes records flagged with "deleted=0" when fetching list query
     * (yes, for some reason it seems to work backwards than the variable naming suggests)
     *
     * @see HelperList::$deleted
     * @see AdminController::getWhereClause for original usage
     *
     * @var bool
     */
    private $deleted;

    /**
     * If set to true, then sql all columns select ("a.*") is not used.
     *
     * @see AdminController::$explicitSelect
     * @see AdminController::getList() for original usage
     *
     * @var bool
     */
    private $explicitSelect;

    /**
     * @var bool Use SQL_CALC_FOUND_ROWS / FOUND_ROWS to count the number of records
     *
     * @see AdminController::$_use_found_rows
     */
    private $useFoundRows;

    /**
     * @see ControllerConfiguration::$positionIdentifierKey
     * @see HelperList::$position_identifier
     *
     * @var string|null
     */
    private $positionIdentifierKey;

    /**
     * @see ControllerConfiguration::$bootstrap
     * @see HelperList::$bootstrap
     *
     * @var bool
     */
    private $bootstrap;

    /**
     * @see ControllerConfiguration::$legacyCurrentIndex
     * @see HelperList::$currentIndex
     *
     * @var string|null
     */
    private $legacyCurrentIndex;

    /**
     * Template variables passed to delete action button.
     *
     * @see HelperList::$tpl_delete_link_vars
     *
     * @var array<string, mixed>
     */
    private $deleteLinkVars = [];

    /**
     * @see AdminController::$shopLinkType
     *
     * @var string
     */
    private $shopLinkType = '';

    /**
     * Pagination limit options for list.
     *
     * @see AdminController::$_pagination
     * @see HelperList::$_pagination
     *
     * @var int[]
     */
    private $paginationLimits = [20, 50, 100, 300, 1000];

    /**
     * Default pagination limit option for list.
     *
     * @see AdminController::$_default_pagination
     * @see HelperList::$_default_pagination
     *
     * @var int
     */
    private $defaultPaginationLimit = 50;

    /**
     * Actions shown in list toolbar.
     *
     * @see HelperList::$toolbar_btn
     * @see self::addToolbarAction() for array structure definition.
     *
     * @var array<string, array<string, mixed>>
     */
    private $toolbarActions = [];

    /**
     * Actions for every list row.
     *
     * @see HelperList::$actions
     *
     * @var string[]
     */
    private $rowActions = [];

    /**
     * Bulk actions for list.
     *
     * @see HelperList::$bulk_actions
     * @see self::addBulkAction() for array structure definition.
     *
     * @var array<string, array<string, mixed>>
     */
    private $bulkActions = [];

    /**
     * Defines where list filters and other POST actions should be submitted.
     * Without this property, HelperList would always submit back to legacy controller.
     *
     * @see HelperList::$frameworkIndexUrl
     *
     * @var string|null
     */
    private $indexUrl;

    /**
     * If true - joins related shop tables when querying sql.
     *
     * @see AdminController::$shopShareDatas
     *
     * @var bool
     */
    private $shopShareData = false;

    /**
     * @param int $tabId
     * @param string $tableName
     * @param string $listId
     * @param string $objectModelClassName
     * @param string $identifierKey
     * @param string|null $positionIdentifierKey
     * @param bool $isJoinLanguageTableAuto
     * @param bool $deleted
     * @param string $defaultOrderBy
     * @param bool $explicitSelect
     * @param bool $useFoundRows
     * @param string $legacyControllerName
     * @param string $token
     * @param bool $bootstrap
     * @param string $legacyCurrentIndex
     * @param int $multiShopContext
     * @param string $indexUrl
     */
    public function __construct(
        int $tabId,
        string $tableName,
        string $listId,
        string $objectModelClassName,
        string $identifierKey,
        ?string $positionIdentifierKey,
        bool $isJoinLanguageTableAuto,
        bool $deleted,
        string $defaultOrderBy,
        bool $explicitSelect,
        bool $useFoundRows,
        string $legacyControllerName,
        string $token,
        bool $bootstrap,
        string $legacyCurrentIndex,
        int $multiShopContext,
        string $indexUrl
    ) {
        $this->tabId = $tabId;
        $this->tableName = $tableName;
        $this->listId = $listId;
        $this->objectModelClassName = $objectModelClassName;
        $this->identifierKey = $identifierKey;
        $this->positionIdentifierKey = $positionIdentifierKey;
        $this->autoJoinLanguageTable = $isJoinLanguageTableAuto;
        $this->deleted = $deleted;
        $this->defaultOrderBy = $defaultOrderBy;
        $this->explicitSelect = $explicitSelect;
        $this->useFoundRows = $useFoundRows;
        $this->legacyControllerName = $legacyControllerName;
        $this->token = $token;
        $this->bootstrap = $bootstrap;
        $this->legacyCurrentIndex = $legacyCurrentIndex;
        $this->multiShopContext = $multiShopContext;
        $this->indexUrl = $indexUrl;
    }

    /**
     * @return int
     */
    public function getTabId(): int
    {
        return $this->tabId;
    }

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * @return string
     */
    public function getListId(): string
    {
        return $this->listId;
    }

    /**
     * @return string
     */
    public function getObjectModelClassName(): string
    {
        return $this->objectModelClassName;
    }

    /**
     * @return string
     */
    public function getLegacyControllerName(): string
    {
        return $this->legacyControllerName;
    }

    /**
     * @return string
     */
    public function getIdentifierKey(): string
    {
        return $this->identifierKey;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @return string
     */
    public function getDefaultOrderBy(): string
    {
        return $this->defaultOrderBy;
    }

    /**
     * @return bool
     */
    public function autoJoinLanguageTable(): bool
    {
        return $this->autoJoinLanguageTable;
    }

    /**
     * @return int
     */
    public function getMultiShopContext(): int
    {
        return $this->multiShopContext;
    }

    /**
     * @return bool
     */
    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    /**
     * @return bool
     */
    public function isExplicitSelect(): bool
    {
        return $this->explicitSelect;
    }

    /**
     * @return bool
     */
    public function useFoundRows(): bool
    {
        return $this->useFoundRows;
    }

    /**
     * @return string|null
     */
    public function getPositionIdentifierKey(): ?string
    {
        return $this->positionIdentifierKey;
    }

    /**
     * @return bool
     */
    public function isBootstrap(): bool
    {
        return $this->bootstrap;
    }

    /**
     * @return string|null
     */
    public function getLegacyCurrentIndex(): ?string
    {
        return $this->legacyCurrentIndex;
    }

    /**
     * @return string
     */
    public function getDefaultOrderWay(): string
    {
        return $this->defaultOrderWay;
    }

    /**
     * @param string $defaultOrderWay
     *
     * @return HelperListConfiguration
     */
    public function setDefaultOrderWay(string $defaultOrderWay): self
    {
        $this->defaultOrderWay = $defaultOrderWay;

        return $this;
    }

    /**
     * list of vars for delete button
     *
     * @return array<string, mixed>
     */
    public function getDeleteLinkVars(): array
    {
        return $this->deleteLinkVars;
    }

    /**
     * @param array<string, mixed> $deleteLinkVars
     *
     * @return HelperListConfiguration
     */
    public function setDeleteLinkVars(array $deleteLinkVars): HelperListConfiguration
    {
        $this->deleteLinkVars = $deleteLinkVars;

        return $this;
    }

    /**
     * @return string
     */
    public function getShopLinkType(): string
    {
        return $this->shopLinkType;
    }

    /**
     * @param string $shopLinkType
     *
     * @return HelperListConfiguration
     */
    public function setShopLinkType(string $shopLinkType): HelperListConfiguration
    {
        $this->shopLinkType = $shopLinkType;

        return $this;
    }

    /**
     * @return int[]
     */
    public function getPaginationLimits(): array
    {
        return $this->paginationLimits;
    }

    /**
     * @param int[] $paginationLimits
     *
     * @return HelperListConfiguration
     */
    public function setPaginationLimits(array $paginationLimits): HelperListConfiguration
    {
        $this->paginationLimits = $paginationLimits;

        return $this;
    }

    /**
     * @return int
     */
    public function getDefaultPaginationLimit(): int
    {
        return $this->defaultPaginationLimit;
    }

    /**
     * @param int $defaultPaginationLimit
     *
     * @return HelperListConfiguration
     */
    public function setDefaultPaginationLimit(int $defaultPaginationLimit): HelperListConfiguration
    {
        $this->defaultPaginationLimit = $defaultPaginationLimit;

        return $this;
    }

    /**
     * @param string $label
     * @param array{href?:string, desc?:string, class?:string} $config
     *
     * @return $this
     */
    public function addToolbarAction(string $label, array $config): self
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver
            ->setDefined(['href', 'desc', 'class'])
            ->setAllowedTypes('class', ['string', 'null'])
            ->setAllowedTypes('href', ['string', 'null'])
            ->setAllowedTypes('desc', ['string', 'null'])
        ;

        $this->toolbarActions[$label] = $optionsResolver->resolve($config);

        return $this;
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function getToolbarActions(): array
    {
        return $this->toolbarActions;
    }

    /**
     * @param string $action
     *
     * @return $this
     */
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

    /**
     * @param string $label
     * @param array{text: string, icon?: string, confirm?: string} $config
     *
     * @return $this
     */
    public function addBulkAction(string $label, array $config): self
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver
            ->setDefined(['text', 'icon', 'confirm'])
            ->setAllowedTypes('text', ['string'])
            ->setAllowedTypes('icon', ['string', 'null'])
            ->setAllowedTypes('confirm', ['string', 'null'])
        ;

        $this->bulkActions[$label] = $optionsResolver->resolve($config);

        return $this;
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function getBulkActions(): array
    {
        return $this->bulkActions;
    }

    /**
     * @param array<string, array<string, mixed>> $fieldsList
     *
     * @return $this
     */
    public function setFieldsList(array $fieldsList): self
    {
        $optionsResolver = new OptionsResolver();

        $optionsResolver
            ->setRequired([
                'title',
            ])
            ->setDefined([
                'align',
                'class',
                'filter_type',
                'filter_key',
                'orderby',
                'position',
                'search',
                'width',
                'havingFilter',
                'icon',
                'list',
            ])
            ->addAllowedTypes('title', 'string')
            ->addAllowedTypes('orderby', 'boolean')
            ->addAllowedTypes('search', 'boolean')
            ->addAllowedTypes('havingFilter', 'boolean')
            ->setDefaults([
                'havingFilter' => false,
            ])
        ;

        foreach ($fieldsList as $field => $config) {
            $this->fieldsList[$field] = $optionsResolver->resolve($config);
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function getIndexUrl(): ?string
    {
        return $this->indexUrl;
    }

    /**
     * @return bool
     */
    public function isShopShareData(): bool
    {
        return $this->shopShareData;
    }

    /**
     * @param bool $shopShareData
     *
     * @return HelperListConfiguration
     */
    public function setShopShareData(bool $shopShareData): self
    {
        $this->shopShareData = $shopShareData;

        return $this;
    }
}
