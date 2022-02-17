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

namespace PrestaShopBundle\Bridge\Helper;

use Configuration;
use Context;
use Db;
use DbQuery;
use HelperList;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShopBundle\Bridge\Controller\ControllerConfiguration;
use PrestaShopBundle\Security\Admin\Employee;
use PrestaShopException;
use Shop;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Tools;
use Validate;

/**
 * A bridge to use helper list to render list in Controller migrate horizontally
 */
class HelperListBridge
{
    /**
     * @var Context
     */
    private $context;

    /**
     * @var Employee
     */
    private $user;

    /**
     * @var HelperListVarsAssigner
     */
    private $helperListVarsAssigner;

    /**
     * @var HookDispatcherInterface
     */
    private $hookDispatcher;

    public function __construct(
        LegacyContext $legacyContext,
        TokenStorage $tokenStorage,
        HelperListVarsAssigner $helperListVarsAssigner,
        HookDispatcherInterface $hookDispatcher
    ) {
        $this->context = $legacyContext->getContext();
        $this->user = $this->getUser($tokenStorage);
        $this->helperListVarsAssigner = $helperListVarsAssigner;
        $this->hookDispatcher = $hookDispatcher;
    }

    /**
     * Render list content
     *
     * @var HelperListConfiguration
     *
     * @return string
     */
    public function renderList(
        ControllerConfiguration $controllerConfiguration,
        HelperListConfiguration $helperListConfiguration
    ): ?string {
        if (!($helperListConfiguration->fieldsList && is_array($helperListConfiguration->fieldsList))) {
            return null;
        }

        $helper = new HelperList();

        $this->helperListVarsAssigner->setHelperDisplay($controllerConfiguration, $helperListConfiguration, $helper);
        $helper->_default_pagination = $helperListConfiguration->defaultPagination;
        $helper->_pagination = $helperListConfiguration->pagination;
        $helper->tpl_delete_link_vars = $helperListConfiguration->deleteLinksVariableTemplate;

        // For compatibility reasons, we have to check standard actions in class attributes
        foreach ($controllerConfiguration->actionsAvailable as $action) {
            if (!in_array($action, $controllerConfiguration->actions) && isset($controllerConfiguration->$action) && $controllerConfiguration->$action) {
                $controllerConfiguration->actions[] = $action;
            }
        }

        /* @phpstan-ignore-next-line */
        $helper->sql = $helperListConfiguration->listsql;

        return $helper->generateList($helperListConfiguration->list, $controllerConfiguration->fieldsList);
    }

    public function getList(
        HelperListConfiguration $helperListConfiguration,
        int $id_lang,
        $orderBy = null,
        $orderWay = null,
        $start = 0,
        $limit = null,
        $idLangShop = false
    ) {
        if ($helperListConfiguration->table == 'feature_value') {
            $helperListConfiguration->where .= ' AND (a.custom = 0 OR a.custom IS NULL)';
        }

        $this->hookDispatcher->dispatchWithParameters('action' . $helperListConfiguration->controllerNameLegacy . 'ListingFieldsModifier', [
            'select' => &$helperListConfiguration->select,
            'join' => &$helperListConfiguration->join,
            'where' => &$helperListConfiguration->where,
            'group_by' => &$helperListConfiguration->group,
            'order_by' => &$helperListConfiguration->orderBy,
            'order_way' => &$helperListConfiguration->orderWay,
            'fields' => &$helperListConfiguration->fieldsList,
        ]);

        if (!Validate::isTableOrIdentifier($helperListConfiguration->table)) {
            throw new PrestaShopException(sprintf('Table name %s is invalid:', $helperListConfiguration->table));
        }

        /* Check params validity */
        if (!is_numeric($start) || !Validate::isUnsignedId($id_lang)) {
            throw new PrestaShopException('get list params is not valid');
        }

        $limit = $this->checkSqlLimit($helperListConfiguration, $limit);

        /* Determine offset from current page */
        $start = 0;
        if ((int) Tools::getValue('submitFilter' . $helperListConfiguration->listId)) {
            $start = ((int) Tools::getValue('submitFilter' . $helperListConfiguration->listId) - 1) * $limit;
        } elseif (
            empty($start)
            && isset($this->context->cookie->{$helperListConfiguration->listId . '_start'})
            && Tools::isSubmit('export' . $helperListConfiguration->table)
        ) {
            $start = $this->context->cookie->{$helperListConfiguration->listId . '_start'};
        }

        // Either save or reset the offset in the cookie
        if ($start) {
            $this->context->cookie->{$helperListConfiguration->listId . '_start'} = $start;
        } elseif (isset($this->context->cookie->{$helperListConfiguration->listId . '_start'})) {
            unset($this->context->cookie->{$helperListConfiguration->listId . '_start'});
        }

        // Add SQL shop restriction
        $select_shop = '';
        if ($helperListConfiguration->shopLinkType) {
            $select_shop = ', shop.name as shop_name ';
        }

        if ($helperListConfiguration->multishopContext && Shop::isTableAssociated($helperListConfiguration->table) && !empty($helperListConfiguration->className)) {
            if (Shop::getContext() != Shop::CONTEXT_ALL || !$this->user->getData()->isSuperAdmin()) {
                $test_join = !preg_match('#`?' . preg_quote(_DB_PREFIX_ . $helperListConfiguration->table . '_shop') . '`? *sa#', $helperListConfiguration->join);
                if (Shop::isFeatureActive() && $test_join && Shop::isTableAssociated($helperListConfiguration->table)) {
                    $helperListConfiguration->where .= ' AND EXISTS (
                            SELECT 1
                            FROM `' . _DB_PREFIX_ . $helperListConfiguration->table . '_shop` sa
                            WHERE a.`' . bqSQL($helperListConfiguration->identifier) . '` = sa.`' . bqSQL($helperListConfiguration->identifier) . '`
                             AND sa.id_shop IN (' . implode(', ', Shop::getContextListShopID()) . ')
                        )';
                }
            }
        }

        $fromClause = $this->getFromClause($helperListConfiguration);
        $joinClause = $this->getJoinClause($helperListConfiguration, $id_lang, $idLangShop);
        $whereClause = $this->getWhereClause($helperListConfiguration);
        $orderByClause = $this->getOrderByClause($helperListConfiguration, $orderBy, $orderWay);

        $shouldLimitSqlResults = $this->shouldLimitSqlResults($limit);

        do {
            $helperListConfiguration->listsql = '';

            if ($helperListConfiguration->explicitSelect) {
                foreach ($helperListConfiguration->fieldsList as $key => $array_value) {
                    if (isset($helperListConfiguration->select) && preg_match('/[\s]`?' . preg_quote($key, '/') . '`?\s*,/', $helperListConfiguration->select)) {
                        continue;
                    }

                    if (isset($array_value['filter_key'])) {
                        $helperListConfiguration->listsql .= str_replace('!', '.`', $array_value['filter_key']) . '` AS `' . $key . '`, ';
                    } elseif ($key == 'id_' . $helperListConfiguration->table) {
                        $helperListConfiguration->listsql .= 'a.`' . bqSQL($key) . '`, ';
                    } elseif ($key != 'image' && !preg_match('/' . preg_quote($key, '/') . '/i', $helperListConfiguration->select)) {
                        $helperListConfiguration->listsql .= '`' . bqSQL($key) . '`, ';
                    }
                }
                $helperListConfiguration->listsql = rtrim(trim($helperListConfiguration->listsql), ',');
            } else {
                $helperListConfiguration->listsql .= ($helperListConfiguration->isJoinLanguageTableAuto ? 'b.*,' : '') . ' a.*';
            }

            $helperListConfiguration->listsql .= "\n" . (isset($helperListConfiguration->select) ? ', ' . rtrim($helperListConfiguration->select, ', ') : '') . $select_shop;

            $limitClause = ' ' . (($shouldLimitSqlResults) ? ' LIMIT ' . (int) $start . ', ' . (int) $limit : '');

            if ($helperListConfiguration->useFoundRows || isset($helperListConfiguration->filterHaving) || isset($helperListConfiguration->having)) {
                $helperListConfiguration->listsql = 'SELECT SQL_CALC_FOUND_ROWS ' .
                    $helperListConfiguration->listsql .
                    $fromClause .
                    $joinClause .
                    $whereClause .
                    $orderByClause .
                    $limitClause;

                $list_count = 'SELECT FOUND_ROWS() AS `' . _DB_PREFIX_ . $helperListConfiguration->table . '`';
            } else {
                $helperListConfiguration->listsql = 'SELECT ' .
                    $helperListConfiguration->listsql .
                    $fromClause .
                    $joinClause .
                    $whereClause .
                    $orderByClause .
                    $limitClause;

                $list_count = 'SELECT COUNT(*) AS `' . _DB_PREFIX_ . $helperListConfiguration->table . '` ' .
                    $fromClause .
                    $joinClause .
                    $whereClause;
            }

            $helperListConfiguration->list = Db::getInstance()->executeS($helperListConfiguration->listsql, true, false);

            if ($helperListConfiguration->list === false) {
                $helperListConfiguration->listError = Db::getInstance()->getMsgError();

                break;
            }

            $helperListConfiguration->listTotal = Db::getInstance()->getValue($list_count, false);

            if ($shouldLimitSqlResults) {
                $start = (int) $start - (int) $limit;
                if ($start < 0) {
                    break;
                }
            } else {
                break;
            }
        } while (empty($this->_list));

        if ($helperListConfiguration->table == 'feature') {
            $nbItems = count($helperListConfiguration->list);
            for ($i = 0; $i < $nbItems; ++$i) {
                $item = &$helperListConfiguration->list[$i];

                $query = new DbQuery();
                $query->select('COUNT(fv.id_feature_value) as count_values');
                $query->from('feature_value', 'fv');
                $query->where('fv.id_feature =' . (int) $item['id_feature']);
                $query->where('(fv.custom=0 OR fv.custom IS NULL)');
                $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
                $item['value'] = (int) $res;
                unset($query);
            }
        }

        $this->hookDispatcher->dispatchWithParameters('action' . $helperListConfiguration->controllerNameLegacy . 'ListingResultsModifier', [
            'list' => &$helperListConfiguration->list,
            'list_total' => &$helperListConfiguration->listTotal,
        ]);
    }

    private function getUser(TokenStorageInterface $tokenStorage)
    {
        if (null === $token = $tokenStorage->getToken()) {
            return null;
        }

        if (!\is_object($user = $token->getUser())) {
            return null;
        }

        if (!$user instanceof Employee) {
            return null;
        }

        return $user;
    }

    /**
     * @param int $limit
     *
     * @return int
     */
    private function checkSqlLimit(HelperListConfiguration $helperListConfiguration, $limit)
    {
        if (empty($limit)) {
            if (
                isset($this->context->cookie->{$helperListConfiguration->listId . '_pagination'}) &&
                $this->context->cookie->{$helperListConfiguration->listId . '_pagination'}
            ) {
                $limit = $this->context->cookie->{$helperListConfiguration->listId . '_pagination'};
            } else {
                $limit = $helperListConfiguration->defaultPagination;
            }
        }

        $limit = (int) Tools::getValue($helperListConfiguration->listId . '_pagination', $limit);
        if (in_array($limit, $helperListConfiguration->pagination) && $limit != $helperListConfiguration->defaultPagination) {
            $this->context->cookie->{$helperListConfiguration->listId . '_pagination'} = $limit;
        } else {
            unset($this->context->cookie->{$helperListConfiguration->listId . '_pagination'});
        }

        if (!is_numeric($limit)) {
            throw new PrestaShopException('Invalid limit. It should be a numeric.');
        }

        return $limit;
    }

    /**
     * @return string
     */
    private function getFromClause(HelperListConfiguration $helperListConfiguration)
    {
        $sql_table = $helperListConfiguration->table == 'order' ? 'orders' : $helperListConfiguration->table;

        return "\n" . 'FROM `' . _DB_PREFIX_ . $sql_table . '` a ';
    }

    /**
     * @param int $id_lang
     * @param int $id_lang_shop
     *
     * @return string
     */
    private function getJoinClause(HelperListConfiguration $helperListConfiguration, $id_lang, $id_lang_shop)
    {
        $shopJoinClause = '';
        if ($helperListConfiguration->shopLinkType) {
            $shopJoinClause = ' LEFT JOIN `' . _DB_PREFIX_ . bqSQL($helperListConfiguration->shopLinkType) . '` shop
                            ON a.`id_' . bqSQL($helperListConfiguration->shopLinkType) . '` = shop.`id_' . bqSQL($helperListConfiguration->shopLinkType) . '`';
        }

        return "\n" . $this->getLanguageJoinClause($helperListConfiguration, $id_lang, $id_lang_shop) .
            "\n" . (isset($this->_join) ? $this->_join . ' ' : '') .
            "\n" . $shopJoinClause;
    }

    /**
     * @param int $idLang
     * @param int $idLangShop
     *
     * @return string
     */
    private function getLanguageJoinClause(HelperListConfiguration $helperListConfiguration, $idLang, $idLangShop)
    {
        $languageJoinClause = '';
        if ($helperListConfiguration->isJoinLanguageTableAuto) {
            $languageJoinClause = 'LEFT JOIN `' . _DB_PREFIX_ . bqSQL($helperListConfiguration->table) . '_lang` b
                ON (b.`' . bqSQL($helperListConfiguration->identifier) . '` = a.`' . bqSQL($helperListConfiguration->identifier) . '` AND b.`id_lang` = ' . (int) $idLang;

            if ($idLangShop) {
                if (!Shop::isFeatureActive()) {
                    $languageJoinClause .= ' AND b.`id_shop` = ' . (int) Configuration::get('PS_SHOP_DEFAULT');
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
     * @return string
     */
    private function getWhereClause(HelperListConfiguration $helperListConfiguration): string
    {
        $whereShop = '';
        if ($helperListConfiguration->shopLinkType) {
            //$whereShop = Shop::addSqlRestriction($this->shopShareDatas, 'a');
            $whereShop = Shop::addSqlRestriction(false, 'a');
        }

        return ' WHERE 1 ' . (isset($helperListConfiguration->where) ? $helperListConfiguration->where . ' ' : '') .
            ($helperListConfiguration->deleted ? 'AND a.`deleted` = 0 ' : '') .
            (isset($helperListConfiguration->filter) ? $helperListConfiguration->filter : '') . $whereShop . "\n" .
            (isset($helperListConfiguration->group) ? $helperListConfiguration->group . ' ' : '') . "\n" .
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
     * @param string|null $orderBy
     * @param string|null $orderDirection
     *
     * @return string
     */
    private function getOrderByClause(HelperListConfiguration $helperListConfiguration, $orderBy, $orderDirection)
    {
        $helperListConfiguration->orderBy = $this->checkOrderBy($helperListConfiguration, $orderBy);
        $helperListConfiguration->orderWay = $this->checkOrderDirection($helperListConfiguration, $orderDirection);

        return ' ORDER BY '
            . ((str_replace('`', '', $helperListConfiguration->orderBy) == $helperListConfiguration->identifier) ? 'a.' : '')
            . $helperListConfiguration->orderBy
            . ' '
            . $helperListConfiguration->orderWay;
    }

    /**
     * @param string|null $orderBy
     *
     * @return false|string
     */
    private function checkOrderBy(HelperListConfiguration $helperListConfiguration, $orderBy)
    {
        if (empty($orderBy)) {
            $prefix = $this->getCookieFilterPrefix($helperListConfiguration);

            if ($this->context->cookie->{$prefix . $helperListConfiguration->listId . 'Orderby'}) {
                $orderBy = $this->context->cookie->{$prefix . $helperListConfiguration->listId . 'Orderby'};
            } elseif ($helperListConfiguration->orderBy) {
                $orderBy = $helperListConfiguration->orderBy;
            } else {
                $orderBy = $helperListConfiguration->defaultOrderBy;
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
     * @param string|null $orderDirection
     *
     * @return string
     */
    private function checkOrderDirection(HelperListConfiguration $helperListConfiguration, $orderDirection)
    {
        $prefix = $this->getCookieOrderByPrefix($helperListConfiguration);
        if (empty($orderDirection)) {
            if ($this->context->cookie->{$prefix . $helperListConfiguration->listId . 'Orderway'}) {
                $orderDirection = $this->context->cookie->{$prefix . $helperListConfiguration->listId . 'Orderway'};
            } elseif ($helperListConfiguration->orderWay) {
                $orderDirection = $helperListConfiguration->orderWay;
            } else {
                $orderDirection = $helperListConfiguration->defaultOrderWay;
            }
        }

        if (!Validate::isOrderWay($orderDirection)) {
            throw new PrestaShopException('Invalid order direction.');
        }

        return pSQL(Tools::strtoupper($orderDirection));
    }

    /**
     * @return mixed
     */
    private function getCookieOrderByPrefix(HelperListConfiguration $helperListConfiguration)
    {
        return str_replace(['admin', 'controller'], '', Tools::strtolower($helperListConfiguration->controllerNameLegacy));
    }

    /**
     * Set the filters used for the list display.
     */
    private function getCookieFilterPrefix(HelperListConfiguration $helperListConfiguration)
    {
        return str_replace(['admin', 'controller'], '', Tools::strtolower($helperListConfiguration->controllerNameLegacy));
    }

    private function shouldLimitSqlResults($limit): bool
    {
        return $limit !== false;
    }
}
