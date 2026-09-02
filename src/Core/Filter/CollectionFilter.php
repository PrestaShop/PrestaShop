<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Filter;

/**
 * Iterates over a collection, filtering each element using a queue of filters.
 */
class CollectionFilter implements FilterInterface
{
    /**
     * @var FilterInterface[]
     */
    private $filters = [];

    /**
     * Sets process queue.
     *
     * @param FilterInterface[] $filters
     *
     * @return $this
     *
     * @throws FilterException
     */
    public function queue(array $filters)
    {
        foreach ($filters as $filter) {
            if (!$filter instanceof FilterInterface) {
                throw new FilterException(sprintf('The provided filter is not valid filter: "%s"', print_r($filter, true)));
            }
        }

        $this->filters = $filters;

        return $this;
    }

    /**
     * Returns the current queue.
     *
     * @return FilterInterface[]
     */
    public function getQueue()
    {
        return $this->filters;
    }

    /**
     * Filters the provided subject.
     *
     * @param array $subject Collection to filter
     *
     * @return array
     *
     * @throws FilterException
     */
    public function filter($subject)
    {
        if (!is_array($subject)) {
            throw new FilterException(sprintf('Invalid subject: %s', print_r($subject, true)));
        }

        foreach ($subject as $k => $value) {
            foreach ($this->filters as $filter) {
                $value = $filter->filter($value);
            }
            $subject[$k] = $value;
        }

        return $subject;
    }
}
