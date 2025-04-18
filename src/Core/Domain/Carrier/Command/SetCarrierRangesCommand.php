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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Carrier\Command;

use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\CarrierId;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\CarrierRangesCollection;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/**
 * Command aim to edit carrier range
 */
class SetCarrierRangesCommand
{
    private CarrierId $carrierId;
    private CarrierRangesCollection $ranges;

    public function __construct(
        int $carrierId,
        /* @var array{
         *     id_zone: int,
         *     range_from: float,
         *     range_to: float,
         *     range_price: string,
         * }[] $ranges,
         */
        array $ranges,
        private readonly ShopConstraint $shopConstraint
    ) {
        $this->carrierId = new CarrierId($carrierId);
        $this->ranges = new CarrierRangesCollection($ranges);
    }

    public function getCarrierId(): CarrierId
    {
        return $this->carrierId;
    }

    public function getRanges(): CarrierRangesCollection
    {
        return $this->ranges;
    }

    public function getShopConstraint(): ShopConstraint
    {
        return $this->shopConstraint;
    }
}
