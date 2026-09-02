<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Filter;

/**
 * Class FilterCollection manages filters collection for grid.
 */
final class FilterCollection implements FilterCollectionInterface
{
    /**
     * @var FilterInterface[]
     */
    private $filters = [];

    /**
     * {@inheritdoc}
     */
    public function add(FilterInterface $filter)
    {
        $this->filters[$filter->getName()] = $filter;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($filterName)
    {
        if (isset($this->filters[$filterName])) {
            unset($this->filters[$filterName]);
        }

        return $this;
    }

    /**
     * @param string $filterName
     *
     * @return FilterInterface|null return null if no filter with given filter name
     */
    public function get($filterName)
    {
        if (isset($this->filters[$filterName])) {
            return $this->filters[$filterName];
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return $this->filters;
    }
}
