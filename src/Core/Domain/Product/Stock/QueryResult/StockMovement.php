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

namespace PrestaShop\PrestaShop\Core\Domain\Product\Stock\QueryResult;

use DateTime;

class StockMovement
{
    /**
     * @var int
     */
    protected $stockMovementId;

    /**
     * @var int
     */
    protected $stockId;

    /**
     * @var int
     */
    protected $stockMovementReasonId;

    /**
     * @var int
     */
    protected $deltaQuantity;

    /**
     * @var DateTime
     */
    protected $dateAdd;

    /**
     * @param int $stockMovementId
     * @param int $stockId
     * @param int $stockMovementReasonId
     * @param int $deltaQuantity
     */
    public function __construct(
        int $stockMovementId,
        int $stockId,
        int $stockMovementReasonId,
        int $deltaQuantity,
        DateTime $dateAdd
    ) {
        $this->stockMovementId = $stockMovementId;
        $this->stockId = $stockId;
        $this->stockMovementReasonId = $stockMovementReasonId;
        $this->deltaQuantity = $deltaQuantity;
        $this->dateAdd = $dateAdd;
    }

    /**
     * @return int
     */
    public function getStockMovementId(): int
    {
        return $this->stockMovementId;
    }

    /**
     * @return int
     */
    public function getStockId(): int
    {
        return $this->stockId;
    }

    /**
     * @return int
     */
    public function getStockMovementReasonId(): int
    {
        return $this->stockMovementReasonId;
    }

    /**
     * @return int
     */
    public function getDeltaQuantity(): int
    {
        return $this->deltaQuantity;
    }

    /**
     * @return DateTime
     */
    public function getDateAdd(): DateTime
    {
        return $this->dateAdd;
    }
}
