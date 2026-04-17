<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Search;

use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Grid\Search\ShopSearchCriteriaInterface;

/**
 * ShopFilters contains a ShopConstraint to handle multishop feature, the shop constraint is mandatory and immutable.
 * The class can't be used as is and needs to be used as a parent, you should respect the parameters orders in your
 * implementation (if you don't you'll have to handle the specific build via a TypedBuilderInterface dedicated service).
 */
abstract class ShopFilters extends Filters implements ShopSearchCriteriaInterface
{
    /**
     * @var ShopConstraint
     */
    private $shopConstraint;

    /**
     * @param ShopConstraint $shopConstraint
     * @param array<string, mixed> $filters
     * @param string $filterId
     */
    public function __construct(ShopConstraint $shopConstraint, array $filters = [], $filterId = '')
    {
        parent::__construct($filters, $filterId);
        $this->shopConstraint = $shopConstraint;
    }

    public function getShopConstraint(): ShopConstraint
    {
        return $this->shopConstraint;
    }
}
