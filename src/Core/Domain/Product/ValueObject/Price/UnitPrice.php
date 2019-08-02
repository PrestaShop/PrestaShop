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

namespace PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Price;

use PrestaShop\Decimal\Number;
use PrestaShop\PrestaShop\Core\Domain\Exception\DomainConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Price;

/**
 * @todo: split my params. DO that for whole price.
 * Price per unit - e.g 10 per kilo.
 */
class UnitPrice
{
    /**
     * @var Price
     */
    private $price;

    /**
     * @var string
     */
    private $unit;

    /**
     * @param float $price
     * @param string $unit
     *
     * @throws ProductConstraintException
     */
    public function __construct(float $price, string $unit)
    {
        try {
            $this->price = new Price($price);
        } catch (DomainConstraintException $e) {
            throw new ProductConstraintException(
                'Invalid products unit price',
                ProductConstraintException::INVALID_UNIT_PRICE,
                $e
            );
        }

        $this->unit = $unit;
    }

    /**
     * @return Price
     */
    public function getPrice(): Price
    {
        return $this->price;
    }

    /**
     * @return string
     */
    public function getUnit(): string
    {
        return $this->unit;
    }
}
