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

namespace PrestaShop\PrestaShop\Core\Grid\Search;

use Symfony\Component\HttpFoundation\Request;

/**
 * Class TemporarySearchCriteria is temporary search criteria class. Should be removed.
 */
class TemporarySearchCriteria implements SearchCriteriaInterface
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function getOrderBy()
    {
        return $this->request->get('orderBy', 'id_log');
    }

    public function getOrderWay()
    {
        return $this->request->get('sortOrder', 'asc');
    }

    public function getOffset()
    {
        return $this->request->get('offset', 0);
    }

    public function getLimit()
    {
        return $this->request->get('limit', 10);
    }

    public function getFilters()
    {
        $filters = $this->request->get('logs', []);

        unset($filters['_token']);

        return $filters;
    }
}
