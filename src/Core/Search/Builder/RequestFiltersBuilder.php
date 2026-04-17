<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
    public function buildFilters(?Filters $filters = null)
    {
        if (null === $this->request) {
            return $filters;
        }

        $filterId = $this->getFilterId($filters);
        $queryParams = $this->request->query->all();
        $requestParams = $this->request->request->all();

        // If filters have a filterId then parameters are sent in a namespace (eg: grid_id[limit]=10 instead of limit=10)
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
