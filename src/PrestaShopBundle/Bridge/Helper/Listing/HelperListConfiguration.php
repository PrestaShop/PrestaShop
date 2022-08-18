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

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This object contains the configuration needed to generate a list using the helper list.
 */
class HelperListConfiguration
{
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
     * @var array
     */
    public $fieldsList = [];

    /**
     * @var string
     */
    public $listSql;

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
     * @var string
     */
    public $orderWay = '';

    /**
     * @var int
     */
    private $tabId;

    /**
     * @var string
     */
    private $tableName;

    /**
     * @var string
     */
    private $listId;

    /**
     * @var string
     */
    private $objectModelClassName;

    /**
     * @var string
     */
    private $legacyControllerName;

    /**
     * @var string
     */
    private $identifier;

    /**
     * @var string
     */
    private $token;

    /**
     * @var string
     */
    private $defaultOrderBy;

    /**
     * @var string
     */
    private $defaultOrderWay = 'ASC';

    /**
     * @var bool
     */
    private $autoJoinLanguageTable;

    /**
     * @var int
     */
    private $multiShopContext;

    /**
     * @var bool
     */
    private $deleted;

    /**
     * @var bool
     */
    private $explicitSelect;

    /**
     * @var bool Use SQL_CALC_FOUND_ROWS / FOUND_ROWS to count the number of records
     */
    private $useFoundRows;

    /**
     * @var string
     */
    private $positionIdentifier;

    /**
     * @var bool
     */
    private $bootstrap;

    /**
     * @var string|null
     */
    private $legacyCurrentIndex;

    /**
     * @var array<string, mixed>
     */
    private $deleteLinkVars = [];

    /**
     * @var string
     */
    private $shopLinkType = '';

    /**
     * @var int[]
     */
    private $paginationLimits = [20, 50, 100, 300, 1000];

    /**
     * @var int
     */
    private $defaultPaginationLimit = 50;

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
     * @var string|null
     */
    private $filtersSubmitUrl;

    /**
     * @param int $tabId
     */
    public function __construct(
        int $tabId,
        string $tableName,
        string $listId,
        string $objectModelClassName,
        string $identifier,
        string $positionIdentifier,
        bool $isJoinLanguageTableAuto,
        bool $deleted,
        string $defaultOrderBy,
        bool $explicitSelect,
        bool $useFoundRows,
        string $legacyControllerName,
        string $token,
        bool $bootstrap,
        string $legacyCurrentIndex,
        int $multiShopContext
    ) {
        $this->tabId = $tabId;
        $this->tableName = $tableName;
        $this->listId = $listId;
        $this->objectModelClassName = $objectModelClassName;
        $this->identifier = $identifier;
        $this->positionIdentifier = $positionIdentifier;
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
    public function getIdentifier(): string
    {
        return $this->identifier;
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
     * @return string
     */
    public function getPositionIdentifier(): string
    {
        return $this->positionIdentifier;
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
     * @param array $config
     *
     * @return $this
     */
    public function addToolbarAction(string $label, array $config): self
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver
            ->setDefined(['href', 'desc', 'class'])
            ->setDefaults(['class' => ''])
            ->setAllowedTypes('class', ['string'])
            ->setAllowedTypes('href', ['string'])
            ->setAllowedTypes('desc', ['string'])
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
     * @param array<string, mixed> $config
     *
     * @return $this
     */
    public function addBulkAction(string $label, array $config): self
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver
            ->setDefaults([
                'text' => '',
                'icon' => null,
                'confirm' => null,
            ])
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
     * @return string|null
     */
    public function getFiltersSubmitUrl(): ?string
    {
        return $this->filtersSubmitUrl;
    }

    /**
     * @param string|null $filtersSubmitUrl
     *
     * @return $this
     */
    public function setFiltersSubmitUrl(?string $filtersSubmitUrl): self
    {
        $this->filtersSubmitUrl = $filtersSubmitUrl;

        return $this;
    }
}
