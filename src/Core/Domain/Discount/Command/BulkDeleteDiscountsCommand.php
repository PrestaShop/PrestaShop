<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
