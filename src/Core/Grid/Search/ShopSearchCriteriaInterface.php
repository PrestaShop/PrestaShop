<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Search;

use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/**
 * This extended interface of the SearchCriteriaInterface integrates a mandatory ShopConstraint
 * which is used for multishop feature.
 */
interface ShopSearchCriteriaInterface extends SearchCriteriaInterface
{
    public function getShopConstraint(): ?ShopConstraint;
}
