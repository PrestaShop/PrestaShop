<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Search\Builder;

use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Search\Filters;

/**
 * Basic abstract class for FiltersBuilder classes, able to store the filters_id
 * from the config.
 */
abstract class AbstractFiltersBuilder implements FiltersBuilderInterface
{
    /** @var string */
    protected $filterId;

    /**
     * @var ShopConstraint|null
     */
    protected $shopConstraint;

    /**
     * {@inheritdoc}
     */
    public function setConfig(array $config)
    {
        $this->filterId = $config['filter_id'] ?? '';
        if (isset($config['shop_constraint']) && $config['shop_constraint'] instanceof ShopConstraint) {
            $this->shopConstraint = $config['shop_constraint'];
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    abstract public function buildFilters(?Filters $filters = null);

    /**
     * @param Filters|null $filters
     *
     * @return string
     */
    protected function getFilterId(?Filters $filters = null)
    {
        if (null === $filters) {
            return $this->filterId;
        }

        return $filters->getFilterId();
    }
}
