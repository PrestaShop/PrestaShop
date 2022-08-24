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

namespace PrestaShopBundle\Bridge\Helper\Listing\HelperBridge;

use Context;
use Db;
use HelperList;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShopBundle\Bridge\AdminController\FrameworkBridgeControllerInterface;
use PrestaShopBundle\Bridge\Helper\Listing\FilterPrefix;
use PrestaShopBundle\Bridge\Helper\Listing\HelperListConfiguration;
use PrestaShopBundle\Bridge\Smarty\BreadcrumbsAndTitleConfigurator;
use PrestaShopBundle\Service\DataProvider\UserProvider;
use PrestaShopException;
use Shop;
use Tools;
use Validate;

/**
 * Acts as a bridge between symfony controller and the legacy HelperList to allow rendering the list
 *
 * @see HelperList
 * @see FrameworkBridgeControllerInterface
 */
class HelperListBridge
{
    /**
     * @var Context
     */
    private $context;

    /**
     * @var UserProvider
     */
    private $userProvider;

    /**
     * @var HookDispatcherInterface
     */
    private $hookDispatcher;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var BreadcrumbsAndTitleConfigurator
     */
    private $breadcrumbsAndTitleConfigurator;

    /**
     * @param LegacyContext $legacyContext
     * @param UserProvider $userProvider
     * @param HookDispatcherInterface $hookDispatcher
     * @param Configuration $configuration
     * @param BreadcrumbsAndTitleConfigurator $breadcrumbsAndTitleConfigurator
     */
    public function __construct(
        LegacyContext $legacyContext,
        UserProvider $userProvider,
        HookDispatcherInterface $hookDispatcher,
        Configuration $configuration,
        BreadcrumbsAndTitleConfigurator $breadcrumbsAndTitleConfigurator
    ) {
        $this->context = $legacyContext->getContext();
        $this->userProvider = $userProvider;
        $this->hookDispatcher = $hookDispatcher;
        $this->configuration = $configuration;
        $this->breadcrumbsAndTitleConfigurator = $breadcrumbsAndTitleConfigurator;
    }

    /**
     * Generate the html for list using HelperList class
     *
     * @param HelperListConfiguration $helperListConfiguration
     *
     * @return string|null
     */
    public function generateList(
        HelperListConfiguration $helperListConfiguration
    ): ?string {
        if (!($helperListConfiguration->fieldsList && is_array($helperListConfiguration->fieldsList))) {
            return null;
        }
        $helper = new HelperList();

        /* @phpstan-ignore-next-line */
        $helper->sql = $this->generateListQuery($helperListConfiguration, $this->context->language->id);

        $this->setHelperDisplay($helperListConfiguration, $helper);
        $helper->_default_pagination = $helperListConfiguration->getDefaultPaginationLimit();
        $helper->_pagination = $helperListConfiguration->getPaginationLimits();
        $helper->tpl_delete_link_vars = $helperListConfiguration->getDeleteLinkVars();
        $helper->postSubmitUrl = $helperListConfiguration->getPostSubmitUrl();

        return $helper->generateList($helperListConfiguration->list, $helperListConfiguration->fieldsList);
    }

    /**
     * @param HelperListConfiguration $helperListConfiguration
     * @param int $idLang
     *
     * @return string
     */
    protected function generateListQuery(
        HelperListConfiguration $helperListConfiguration,
        int $idLang
    ): string {
        $this->hookDispatcher->dispatchWithParameters('action' . $helperListConfiguration->getLegacyControllerName() . 'ListingFieldsModifier', [
            'select' => &$helperListConfiguration->select,
            'join' => &$helperListConfiguration->join,
            'where' => &$helperListConfiguration->where,
            'group_by' => &$helperListConfiguration->group,
            'order_by' => &$helperListConfiguration->orderBy,
            'order_way' => &$helperListConfiguration->orderWay,
            'fields' => &$helperListConfiguration->fieldsList,
        ]);

        if (!Validate::isTableOrIdentifier($helperListConfiguration->getTableName())) {
            throw new PrestaShopException(sprintf('Table name %s is invalid:', $helperListConfiguration->getTableName()));
        }

        $limit = $this->checkSqlLimit($helperListConfiguration);

        /* Determine offset from current page */
        $start = 0;
        if ((int) Tools::getValue('submitFilter' . $helperListConfiguration->getListId())) {
            $start = ((int) Tools::getValue('submitFilter' . $helperListConfiguration->getListId()) - 1) * $limit;
        } elseif (
            isset($this->context->cookie->{$helperListConfiguration->getListId() . '_start'})
            && Tools::isSubmit('export' . $helperListConfiguration->getTableName())
        ) {
            $start = $this->context->cookie->{$helperListConfiguration->getListId() . '_start'};
        }

        // Either save or reset the offset in the cookie
        if ($start) {
            $this->context->cookie->{$helperListConfiguration->getListId() . '_start'} = $start;
        } elseif (isset($this->context->cookie->{$helperListConfiguration->getListId() . '_start'})) {
            unset($this->context->cookie->{$helperListConfiguration->getListId() . '_start'});
        }

        // Add SQL shop restriction
        $select_shop = '';
        if ($helperListConfiguration->getShopLinkType()) {
            $select_shop = ', shop.name as shop_name ';
        }

        if ($helperListConfiguration->getMultiShopContext() && Shop::isTableAssociated($helperListConfiguration->getTableName()) && !empty($helperListConfiguration->getObjectModelClassName())) {
            if (Shop::getContext() != Shop::CONTEXT_ALL || !$this->userProvider->getUser()->getData()->isSuperAdmin()) {
                $helperListConfiguration->where .= ' AND EXISTS (
                        SELECT 1
                        FROM `' . _DB_PREFIX_ . $helperListConfiguration->getTableName() . '_shop` sa
                        WHERE a.`' . bqSQL($helperListConfiguration->getIdentifier()) . '` = sa.`' . bqSQL($helperListConfiguration->getIdentifier()) . '`
                         AND sa.id_shop IN (' . implode(', ', Shop::getContextListShopID()) . ')
                    )';
            }
        }

        $fromClause = $this->getFromClause($helperListConfiguration);
        $joinClause = $this->getJoinClause($helperListConfiguration, $idLang);
        $whereClause = $this->getWhereClause($helperListConfiguration);
        $orderByClause = $this->getOrderByClause($helperListConfiguration, $helperListConfiguration->orderBy, $helperListConfiguration->orderWay);

        $shouldLimitSqlResults = $this->shouldLimitSqlResults($limit);

        do {
            $listSql = '';

            if ($helperListConfiguration->isExplicitSelect()) {
                foreach ($helperListConfiguration->fieldsList as $key => $array_value) {
                    if (preg_match('/[\s]`?' . preg_quote($key, '/') . '`?\s*,/', $helperListConfiguration->select)) {
                        continue;
                    }

                    if (isset($array_value['filter_key'])) {
                        $listSql .= str_replace('!', '.`', $array_value['filter_key']) . '` AS `' . $key . '`, ';
                    } elseif ($key == 'id_' . $helperListConfiguration->getTableName()) {
                        $listSql .= 'a.`' . bqSQL($key) . '`, ';
                    } elseif ($key != 'image' && !preg_match('/' . preg_quote($key, '/') . '/i', $helperListConfiguration->select)) {
                        $listSql .= '`' . bqSQL($key) . '`, ';
                    }
                }
                $listSql = rtrim(trim($listSql), ',');
            } else {
                $listSql .= ($helperListConfiguration->autoJoinLanguageTable() ? 'b.*,' : '') . ' a.*';
            }

            $listSql .= "\n" . (!empty($helperListConfiguration->select) ? ', ' . rtrim($helperListConfiguration->select, ', ') : '') . $select_shop;

            $limitClause = ' ' . (($shouldLimitSqlResults) ? ' LIMIT ' . (int) $start . ', ' . (int) $limit : '');

            if ($helperListConfiguration->useFoundRows()) {
                $listSql = 'SELECT SQL_CALC_FOUND_ROWS ' .
                    $listSql .
                    $fromClause .
                    $joinClause .
                    $whereClause .
                    $orderByClause .
                    $limitClause;

                $list_count = 'SELECT FOUND_ROWS() AS `' . _DB_PREFIX_ . $helperListConfiguration->getTableName() . '`';
            } else {
                $listSql = 'SELECT ' .
                    $listSql .
                    $fromClause .
                    $joinClause .
                    $whereClause .
                    $orderByClause .
                    $limitClause;

                $list_count = 'SELECT COUNT(*) AS `' . _DB_PREFIX_ . $helperListConfiguration->getTableName() . '` ' .
                    $fromClause .
                    $joinClause .
                    $whereClause;
            }

            $helperListConfiguration->list = Db::getInstance()->executeS($listSql, true, false);

            if ($helperListConfiguration->list === false) {
                // @todo: the listError doesn't seem to be used anywhere else yet. Do we need to add some additional list error handler?
                $helperListConfiguration->listError = Db::getInstance()->getMsgError();

                break;
            }

            $helperListConfiguration->listTotal = (int) Db::getInstance()->getValue($list_count, false);

            if ($shouldLimitSqlResults) {
                $start = (int) $start - (int) $limit;
                if ($start < 0) {
                    break;
                }
            } else {
                break;
            }
        } while (empty($helperListConfiguration->list));

        $this->hookDispatcher->dispatchWithParameters('action' . $helperListConfiguration->getLegacyControllerName() . 'ListingResultsModifier', [
            'list' => &$helperListConfiguration->list,
            'list_total' => &$helperListConfiguration->listTotal,
        ]);

        return $listSql;
    }

    /**
     * @param HelperListConfiguration $helperListConfiguration
     * @param string|null $limit
     *
     * @return int
     */
    private function checkSqlLimit(HelperListConfiguration $helperListConfiguration, ?string $limit = null): int
    {
        if (empty($limit)) {
            if (
                isset($this->context->cookie->{$helperListConfiguration->getListId() . '_pagination'}) &&
                $this->context->cookie->{$helperListConfiguration->getListId() . '_pagination'}
            ) {
                $limit = $this->context->cookie->{$helperListConfiguration->getListId() . '_pagination'};
            } else {
                $limit = $helperListConfiguration->getDefaultPaginationLimit();
            }
        }

        $limit = (int) Tools::getValue($helperListConfiguration->getListId() . '_pagination', $limit);
        if (in_array($limit, $helperListConfiguration->getPaginationLimits()) && $limit != $helperListConfiguration->getDefaultPaginationLimit()) {
            $this->context->cookie->{$helperListConfiguration->getListId() . '_pagination'} = $limit;
        } else {
            unset($this->context->cookie->{$helperListConfiguration->getListId() . '_pagination'});
        }

        if (!is_numeric($limit)) {
            throw new PrestaShopException('Invalid limit. It should be a numeric.');
        }

        return $limit;
    }

    /**
     * @param HelperListConfiguration $helperListConfiguration
     *
     * @return string
     */
    private function getFromClause(HelperListConfiguration $helperListConfiguration)
    {
        // for some reason Order object model db table name is plural, so it is handled here (all other tables matches object model singular name)
        $sqlTable = $helperListConfiguration->getTableName() == 'order' ? 'orders' : $helperListConfiguration->getTableName();

        return "\n" . 'FROM `' . _DB_PREFIX_ . $sqlTable . '` a ';
    }

    /**
     * @param HelperListConfiguration $helperListConfiguration
     * @param int $idLang
     * @param int|bool $idLangShop
     *
     * @return string
     */
    private function getJoinClause(HelperListConfiguration $helperListConfiguration, $idLang, $idLangShop = false)
    {
        $shopJoinClause = '';
        if ($helperListConfiguration->getShopLinkType()) {
            $shopJoinClause = ' LEFT JOIN `' . _DB_PREFIX_ . bqSQL($helperListConfiguration->getShopLinkType()) . '` shop
                            ON a.`id_' . bqSQL($helperListConfiguration->getShopLinkType()) . '` = shop.`id_' . bqSQL($helperListConfiguration->getShopLinkType()) . '`';
        }

        return "\n" . $this->getLanguageJoinClause($helperListConfiguration, $idLang, $idLangShop) .
            "\n" . $helperListConfiguration->join . ' ' .
            "\n" . $shopJoinClause;
    }

    /**
     * @param HelperListConfiguration $helperListConfiguration
     * @param int $idLang
     * @param int $idLangShop
     *
     * @return string
     */
    private function getLanguageJoinClause(HelperListConfiguration $helperListConfiguration, $idLang, $idLangShop)
    {
        $languageJoinClause = '';
        if ($helperListConfiguration->autoJoinLanguageTable()) {
            $languageJoinClause = 'LEFT JOIN `' . _DB_PREFIX_ . bqSQL($helperListConfiguration->getTableName()) . '_lang` b
                ON (b.`' . bqSQL($helperListConfiguration->getIdentifier()) . '` = a.`' . bqSQL($helperListConfiguration->getIdentifier()) . '` AND b.`id_lang` = ' . (int) $idLang;

            if ($idLangShop) {
                if (!Shop::isFeatureActive()) {
                    $languageJoinClause .= ' AND b.`id_shop` = ' . (int) $this->configuration->get('PS_SHOP_DEFAULT');
                } elseif (Shop::getContext() == Shop::CONTEXT_SHOP) {
                    $languageJoinClause .= ' AND b.`id_shop` = ' . (int) $idLangShop;
                } else {
                    $languageJoinClause .= ' AND b.`id_shop` = a.id_shop_default';
                }
            }
            $languageJoinClause .= ')';
        }

        return $languageJoinClause;
    }

    /**
     * @param HelperListConfiguration $helperListConfiguration
     *
     * @return string
     */
    private function getWhereClause(HelperListConfiguration $helperListConfiguration): string
    {
        $whereShop = '';
        if ($helperListConfiguration->getShopLinkType()) {
            $whereShop = Shop::addSqlRestriction($helperListConfiguration->isShopShareData(), 'a');
        }

        return ' WHERE 1 ' . $helperListConfiguration->where . ' ' .
            ($helperListConfiguration->isDeleted() ? 'AND a.`deleted` = 0 ' : '') .
            $helperListConfiguration->filter . $whereShop . "\n" .
            $helperListConfiguration->group . ' ' . "\n" .
            $this->getHavingClause();
    }

    /**
     * @return string
     */
    private function getHavingClause(): string
    {
        $havingClause = '';
        if (isset($this->_filterHaving) || isset($this->_having)) {
            $havingClause = ' HAVING ';
            if (isset($this->_filterHaving)) {
                $havingClause .= ltrim($this->_filterHaving, ' AND ');
            }
            if (isset($this->_having)) {
                $havingClause .= $this->_having . ' ';
            }
        }

        return $havingClause;
    }

    /**
     * @param HelperListConfiguration $helperListConfiguration
     * @param string $orderBy
     * @param string $orderDirection
     *
     * @return string
     */
    private function getOrderByClause(HelperListConfiguration $helperListConfiguration, $orderBy, $orderDirection)
    {
        $helperListConfiguration->orderBy = $this->checkOrderBy($helperListConfiguration, $orderBy);
        $helperListConfiguration->orderWay = $this->checkOrderDirection($helperListConfiguration, $orderDirection);

        return ' ORDER BY '
            . ((str_replace('`', '', $helperListConfiguration->orderBy) == $helperListConfiguration->getIdentifier()) ? 'a.' : '')
            . $helperListConfiguration->orderBy
            . ' '
            . $helperListConfiguration->orderWay;
    }

    /**
     * @param HelperListConfiguration $helperListConfiguration
     * @param string $orderBy
     *
     * @return false|string
     */
    private function checkOrderBy(HelperListConfiguration $helperListConfiguration, $orderBy)
    {
        if (empty($orderBy)) {
            $prefix = FilterPrefix::getByClassName($helperListConfiguration->getLegacyControllerName());

            if ($this->context->cookie->{$prefix . $helperListConfiguration->getListId() . 'Orderby'}) {
                $orderBy = $this->context->cookie->{$prefix . $helperListConfiguration->getListId() . 'Orderby'};
            } elseif ($helperListConfiguration->orderBy) {
                $orderBy = $helperListConfiguration->orderBy;
            } else {
                $orderBy = $helperListConfiguration->getDefaultOrderBy();
            }
        }

        /* Check params validity */
        if (!Validate::isOrderBy($orderBy)) {
            throw new PrestaShopException('Invalid "order by" clause.');
        }

        if (!isset($helperListConfiguration->fieldsList[$orderBy]['order_key']) && isset($helperListConfiguration->fieldsList[$orderBy]['filter_key'])) {
            $helperListConfiguration->fieldsList[$orderBy]['order_key'] = $helperListConfiguration->fieldsList[$orderBy]['filter_key'];
        }

        if (isset($helperListConfiguration->fieldsList[$orderBy]['order_key'])) {
            $orderBy = $helperListConfiguration->fieldsList[$orderBy]['order_key'];
        }

        if (preg_match('/[.!]/', $orderBy)) {
            $orderBySplit = preg_split('/[.!]/', $orderBy);
            $orderBy = bqSQL($orderBySplit[0]) . '.`' . bqSQL($orderBySplit[1]) . '`';
        } elseif ($orderBy) {
            $orderBy = bqSQL($orderBy);
        }

        return $orderBy;
    }

    /**
     * @param HelperListConfiguration $helperListConfiguration
     * @param string $orderDirection
     *
     * @return mixed|string
     */
    private function checkOrderDirection(HelperListConfiguration $helperListConfiguration, $orderDirection)
    {
        $prefix = FilterPrefix::getByClassName($helperListConfiguration->getLegacyControllerName());
        if (empty($orderDirection)) {
            if ($this->context->cookie->{$prefix . $helperListConfiguration->getListId() . 'Orderway'}) {
                $orderDirection = $this->context->cookie->{$prefix . $helperListConfiguration->getListId() . 'Orderway'};
            } elseif ($helperListConfiguration->orderWay) {
                $orderDirection = $helperListConfiguration->orderWay;
            } else {
                $orderDirection = $helperListConfiguration->getDefaultOrderWay();
            }
        }

        if (!Validate::isOrderWay($orderDirection)) {
            throw new PrestaShopException('Invalid order direction.');
        }

        return pSQL(Tools::strtoupper($orderDirection));
    }

    /**
     * @param int $limit
     *
     * @return bool
     */
    private function shouldLimitSqlResults($limit): bool
    {
        return $limit !== false;
    }

    /**
     * @param HelperListConfiguration $helperListConfiguration
     * @param HelperList $helper
     */
    private function setHelperDisplay(
        HelperListConfiguration $helperListConfiguration,
        HelperList $helper
    ): void {
        $breadcrumbs = $this->breadcrumbsAndTitleConfigurator->getBreadcrumbs($helperListConfiguration->getTabId());

        $helper->title = $breadcrumbs['tab']['name'];
        $helper->toolbar_btn = $helperListConfiguration->getToolbarActions();
        $helper->actions = $helperListConfiguration->getRowActions();
        $helper->bulk_actions = $helperListConfiguration->getBulkActions();
        $helper->show_toolbar = true;
        $helper->currentIndex = $helperListConfiguration->getLegacyCurrentIndex();
        $helper->table = $helperListConfiguration->getTableName();
        $helper->orderBy = $helperListConfiguration->orderBy;
        $helper->orderWay = $helperListConfiguration->orderWay;
        $helper->listTotal = $helperListConfiguration->listTotal;
        $helper->identifier = $helperListConfiguration->getIdentifier();
        $helper->token = $helperListConfiguration->getToken();
        $helper->position_identifier = $helperListConfiguration->getPositionIdentifier();
        $helper->controller_name = $helperListConfiguration->getLegacyControllerName();
        $helper->list_id = $helperListConfiguration->getListId();
        $helper->bootstrap = $helperListConfiguration->isBootstrap();
    }
}
