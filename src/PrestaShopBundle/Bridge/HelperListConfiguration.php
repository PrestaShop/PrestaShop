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

namespace PrestaShopBundle\Bridge;

use \Shop;

/**
 * Configuration for helper list bridge
 */
class HelperListConfiguration
{
    /**
     * @var string
     */
    public $table;

    /**
     * @var string
     */
    public $listId;

    /**
     * @var string
     */
    public $className;

    /**
     * @var string
     */
    public $identifier;

    /**
     * @var string
     */
    public $select;

    /**
     * @var string
     */
    public $where;

    /**
     * @var string
     */
    public $join;

    /**
     * @var string
     */
    public $group;

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
    public $having;

    /**
     * @var string
     */
    public $filterHaving;

    /**
     * @var array
     */
    public $filter;

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
    public $multishopContext = Shop::CONTEXT_ALL | Shop::CONTEXT_GROUP | Shop::CONTEXT_SHOP;

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
     * @var string
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
}
