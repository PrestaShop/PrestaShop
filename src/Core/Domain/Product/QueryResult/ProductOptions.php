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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\QueryResult;

/**
 * Holds product options information
 */
class ProductOptions
{
    /**
     * @var string
     */
    private $visibility;

    /**
     * @var bool
     */
    private $availableForOrder;

    /**
     * @var bool
     */
    private $onlineOnly;

    /**
     * @var bool
     */
    private $showPrice;

    /**
     * @var string
     */
    private $condition;

    /**
     * @var bool
     */
    private $showCondition;

    /**
     * @var int
     */
    private $manufacturerId;

    /**
     * @param string $visibility
     * @param bool $availableForOrder
     * @param bool $onlineOnly
     * @param bool $showPrice
     * @param string $condition
     * @param bool $showCondition
     * @param int $manufacturerId
     */
    public function __construct(
        string $visibility,
        bool $availableForOrder,
        bool $onlineOnly,
        bool $showPrice,
        string $condition,
        bool $showCondition,
        int $manufacturerId
    ) {
        $this->visibility = $visibility;
        $this->availableForOrder = $availableForOrder;
        $this->onlineOnly = $onlineOnly;
        $this->showPrice = $showPrice;
        $this->condition = $condition;
        $this->showCondition = $showCondition;
        $this->manufacturerId = $manufacturerId;
    }

    /**
     * @return string
     */
    public function getVisibility(): string
    {
        return $this->visibility;
    }

    /**
     * @return bool
     */
    public function isAvailableForOrder(): bool
    {
        return $this->availableForOrder;
    }

    /**
     * @return bool
     */
    public function isOnlineOnly(): bool
    {
        return $this->onlineOnly;
    }

    /**
     * @return bool
     */
    public function showPrice(): bool
    {
        return $this->showPrice;
    }

    /**
     * @return string
     */
    public function getCondition(): string
    {
        return $this->condition;
    }

    /**
     * @return bool
     */
    public function showCondition(): bool
    {
        return $this->showCondition;
    }

    /**
     * @return int
     */
    public function getManufacturerId(): int
    {
        return $this->manufacturerId;
    }
}
