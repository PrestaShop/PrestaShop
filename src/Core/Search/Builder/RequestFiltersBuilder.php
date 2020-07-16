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

namespace PrestaShop\PrestaShop\Core\Search\Builder;

use PrestaShop\PrestaShop\Core\Search\Filters;
use Symfony\Component\HttpFoundation\Request;

/**
 * This builder builds a Filters instance from the request, it is able to fetch the
 * parameters from both GET and POST requests. If the built filter has a filterId
 * it filters the request parameters in a scope (e.g: ?language[limit]=10 instead of
 * ?limit=10)
 * The filterId can be set
 *  - from the builder config
 *  - from the provided filter which class has a default filterId
 *  - from the provided filter which has been manually instantiated with a filterId
 */
final class RequestFiltersBuilder extends AbstractFiltersBuilder
{
    /** @var Request */
    private $request;

    /**
     * {@inheritdoc}
     */
    public function setConfig(array $config)
    {
        $this->request = isset($config['request']) ? $config['request'] : null;

        return parent::setConfig($config);
    }

    /**
     * {@inheritdoc}
     */
    public function buildFilters(Filters $filters = null)
    {
        if (null === $this->request) {
            return $filters;
        }

        $filterId = $this->getFilterId($filters);
        $queryParams = $this->request->query->all();
        $requestParams = $this->request->request->all();

        //If filters have a filterId then parameters are sent in a namespace (eg: grid_id[limit]=10 instead of limit=10)
        if (!empty($filterId)) {
            $queryParams = isset($queryParams[$filterId]) ? $queryParams[$filterId] : [];
            $requestParams = isset($requestParams[$filterId]) ? $requestParams[$filterId] : [];
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
            $filters = new Filters($parameters, $filterId);
        }

        return $filters;
    }
}
