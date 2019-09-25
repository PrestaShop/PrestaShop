<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\QueryResult;

use PrestaShop\Decimal\Number;

/**
 * DTO for order product details
 */
class ProductDetail
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $quantity;

    /**
     * @var Number
     */
    private $taxesPaid;

    /**
     * @var Number
     */
    private $totalPrice;

    /**
     * @param string $name
     * @param int $quantity
     * @param Number $taxesPaid
     * @param Number $totalPrice
     */
    public function __construct(string $name, int $quantity, Number $taxesPaid, Number $totalPrice)
    {
        $this->name = $name;
        $this->quantity = $quantity;
        $this->taxesPaid = $taxesPaid;
        $this->totalPrice = $totalPrice;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Number
     */
    public function getTaxesPaid(): Number
    {
        return $this->taxesPaid;
    }

    /**
     * @return Number
     */
    public function getTotalPrice(): Number
    {
        return $this->totalPrice;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }
}
