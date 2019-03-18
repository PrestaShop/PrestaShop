<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Search\Builder;


use PrestaShop\PrestaShop\Core\Search\AbstractFiltersBuilder;
use PrestaShop\PrestaShop\Core\Search\Filters;
use Symfony\Component\HttpFoundation\Request;

class RequestFiltersBuilder extends AbstractFiltersBuilder
{
    /** @var Request */
    private $request;

    /**
     * @inheritDoc
     */
    public function setConfig(array $config)
    {
        if (isset($config['request'])) {
            $this->request = $config['request'];
        }

        return parent::setConfig($config);
    }

    /**
     * @inheritDoc
     */
    public function buildFilters(Filters $filters = null)
    {
        if (null === $this->request) {
            return $filters;
        }

        $queryParams = $this->request->query->all();
        $requestParams = $this->request->request->all();

        //If filters have a uuid then parameters are sent in a namespace (eg: grid_id[limit]=10 instead of limit=10)
        if (!empty($this->filtersUuid)) {
            $queryParams = isset($queryParams[$this->filtersUuid]) ? $queryParams[$this->filtersUuid] : [];
            $requestParams = isset($requestParams[$this->filtersUuid]) ? $requestParams[$this->filtersUuid] : [];
        }

        $parameters = [];
        foreach (self::FILTER_TYPES as $type) {
            if (isset($queryParams[$type])) {
                $parameters[$type] = $queryParams[$type];
            } elseif (isset($requestParams[$type])) {
                $parameters[$type] = $requestParams[$type];
            }
        }

        if (null !== $filters) {
            $filters->add($parameters);
        } else {
            $filters = new Filters($parameters);
        }

        return $filters;
    }
}
