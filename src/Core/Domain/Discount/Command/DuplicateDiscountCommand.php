<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Discount\Command;

use PrestaShop\PrestaShop\Core\Domain\Discount\ValueObject\DiscountId;

class DuplicateDiscountCommand
{
    private DiscountId $discountId;

    public function __construct(int $discountId)
    {
        $this->discountId = new DiscountId($discountId);
    }

    public function getDiscountId(): DiscountId
    {
        return $this->discountId;
    }
}
