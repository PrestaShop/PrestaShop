<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Filter\FrontEndObject;

use PrestaShop\PrestaShop\Core\Filter\CollectionFilter;
use PrestaShop\PrestaShop\Core\Filter\FilterInterface;

/**
 * Filters the main front end object ("prestashop" on your javascript console).
 */
class MainFilter implements FilterInterface
{
    /**
     * @var FilterInterface[] filters, indexed by key to filter
     */
    private $filters;

    /**
     * @param array $filters FilterInterface[] filters, indexed by key to filter
     */
    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function filter($subject)
    {
        foreach ($this->filters as $key => $filter) {
            if (isset($subject[$key]) && $filter instanceof FilterInterface) {
                if ($filter instanceof CollectionFilter && !is_array($subject[$key])) {
                    continue;
                }

                $subject[$key] = $filter->filter($subject[$key]);
            }
        }

        return $subject;
    }

    /**
     * @return FilterInterface[] filters, indexed by key to filter
     */
    public function getFilters()
    {
        return $this->filters;
    }
}
