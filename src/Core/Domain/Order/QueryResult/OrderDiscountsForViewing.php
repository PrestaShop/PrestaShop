<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Order\QueryResult;

class OrderDiscountsForViewing
{
    /** @var OrderDiscountForViewing[] */
    private $discounts = [];

    /**
     * @param OrderDiscountForViewing[] $discounts
     */
    public function __construct(array $discounts)
    {
        foreach ($discounts as $discount) {
            $this->addDiscount($discount);
        }
    }

    /**
     * @return OrderDiscountForViewing[]
     */
    public function getDiscounts(): array
    {
        return $this->discounts;
    }

    /**
     * @param OrderDiscountForViewing $discount
     */
    private function addDiscount(OrderDiscountForViewing $discount): void
    {
        $this->discounts[] = $discount;
    }
}
