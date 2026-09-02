<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Discount\Command;

use PrestaShop\PrestaShop\Core\Domain\Discount\Exception\DiscountConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Discount\ValueObject\DiscountId;

/**
 * Updates provided discounts to new status
 */
class BulkUpdateDiscountsStatusCommand
{
    /**
     * @var DiscountId[]
     */
    private array $discountIds;

    private bool $newStatus;

    /**
     * @param int[] $discountIds
     * @param bool $newStatus
     *
     * @throws DiscountConstraintException
     */
    public function __construct(array $discountIds, bool $newStatus)
    {
        $this
            ->setDiscountIds($discountIds)
            ->setNewStatus($newStatus)
        ;
    }

    /**
     * @return DiscountId[]
     */
    public function getDiscountIds(): array
    {
        return $this->discountIds;
    }

    public function getNewStatus(): bool
    {
        return $this->newStatus;
    }

    /**
     * @param int[] $discountIds
     *
     * @throws DiscountConstraintException
     */
    private function setDiscountIds(array $discountIds): self
    {
        if (empty($discountIds)) {
            throw new DiscountConstraintException('Missing discounts data for status change');
        }

        foreach ($discountIds as $discountId) {
            $this->discountIds[] = new DiscountId((int) $discountId);
        }

        return $this;
    }

    /**
     * @throws DiscountConstraintException
     */
    private function setNewStatus(bool $newStatus): self
    {
        if (!is_bool($newStatus)) {
            throw new DiscountConstraintException(sprintf('Discount status %s is invalid. Status must be of type "bool".', var_export($newStatus, true)), DiscountConstraintException::INVALID_STATUS);
        }

        $this->newStatus = $newStatus;

        return $this;
    }
}
