<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Discount\Command;

use PrestaShop\PrestaShop\Core\Domain\Discount\Exception\DiscountConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Discount\Exception\DiscountException;
use PrestaShop\PrestaShop\Core\Domain\Discount\ValueObject\DiscountId;

class BulkDeleteDiscountsCommand
{
    /**
     * @var DiscountId[]
     */
    private array $discountIds;

    /**
     * @param int[] $discountIds
     *
     * @throws DiscountConstraintException
     * @throws DiscountException
     */
    public function __construct(array $discountIds)
    {
        $this->setDiscountIds($discountIds);
    }

    /**
     * @return DiscountId[]
     */
    public function getDiscountIds(): array
    {
        return $this->discountIds;
    }

    /**
     * @param int[] $discountIds
     *
     * @throws DiscountConstraintException
     * @throws DiscountException
     */
    private function setDiscountIds(array $discountIds): self
    {
        if (empty($discountIds)) {
            throw new DiscountConstraintException('Missing Discount data for bulk deleting', DiscountConstraintException::INVALID_ID);
        }

        foreach ($discountIds as $discountId) {
            $this->discountIds[] = new DiscountId((int) $discountId);
        }

        return $this;
    }
}
