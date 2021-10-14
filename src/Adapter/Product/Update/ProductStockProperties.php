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

namespace PrestaShop\PrestaShop\Adapter\Product\Update;

use DateTimeInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Pack\ValueObject\PackStockType;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject\OutOfStockType;

class ProductStockProperties
{
    /**
     * @var PackStockType|null
     */
    private $packStockType;

    /**
     * @var int|null
     */
    private $deltaQuantity;

    /**
     * @var OutOfStockType|null
     */
    private $outOfStockType;

    /**
     * @var int|null
     */
    private $minimalQuantity;

    /**
     * @var string|null
     */
    private $location;

    /**
     * @var int|null
     */
    private $lowStockThreshold;

    /**
     * @var bool|null
     */
    private $lowStockAlertEnabled;

    /**
     * @var string[]|null key value pairs where key is the id of language
     */
    private $localizedAvailableNowLabels;

    /**
     * @var string[]|null key value pairs where key is the id of language
     */
    private $localizedAvailableLaterLabels;

    /**
     * @var DateTimeInterface|null
     */
    private $availableDate;

    /**
     * @param PackStockType|null $packStockType
     * @param int|null $deltaQuantity
     * @param OutOfStockType|null $outOfStockType
     * @param int|null $minimalQuantity
     * @param string|null $location
     * @param int|null $lowStockThreshold
     * @param bool|null $lowStockAlertEnabled
     * @param string[]|null $localizedAvailableNowLabels
     * @param string[]|null $localizedAvailableLaterLabels
     * @param DateTimeInterface|null $availableDate
     */
    public function __construct(
        ?PackStockType $packStockType = null,
        ?int $deltaQuantity = null,
        ?OutOfStockType $outOfStockType = null,
        ?int $minimalQuantity = null,
        ?string $location = null,
        ?int $lowStockThreshold = null,
        ?bool $lowStockAlertEnabled = null,
        ?array $localizedAvailableNowLabels = null,
        ?array $localizedAvailableLaterLabels = null,
        ?DateTimeInterface $availableDate = null
    ) {
        $this->packStockType = $packStockType;
        $this->deltaQuantity = $deltaQuantity;
        $this->outOfStockType = $outOfStockType;
        $this->minimalQuantity = $minimalQuantity;
        $this->location = $location;
        $this->lowStockThreshold = $lowStockThreshold;
        $this->lowStockAlertEnabled = $lowStockAlertEnabled;
        $this->localizedAvailableNowLabels = $localizedAvailableNowLabels;
        $this->localizedAvailableLaterLabels = $localizedAvailableLaterLabels;
        $this->availableDate = $availableDate;
    }

    /**
     * @return PackStockType|null
     */
    public function getPackStockType(): ?PackStockType
    {
        return $this->packStockType;
    }

    /**
     * @return int|null
     */
    public function getDeltaQuantity(): ?int
    {
        return $this->deltaQuantity;
    }

    /**
     * @return OutOfStockType|null
     */
    public function getOutOfStockType(): ?OutOfStockType
    {
        return $this->outOfStockType;
    }

    /**
     * @return int|null
     */
    public function getMinimalQuantity(): ?int
    {
        return $this->minimalQuantity;
    }

    /**
     * @return string|null
     */
    public function getLocation(): ?string
    {
        return $this->location;
    }

    /**
     * @return int|null
     */
    public function getLowStockThreshold(): ?int
    {
        return $this->lowStockThreshold;
    }

    /**
     * @return bool|null
     */
    public function isLowStockAlertEnabled(): ?bool
    {
        return $this->lowStockAlertEnabled;
    }

    /**
     * @return string[]|null
     */
    public function getLocalizedAvailableNowLabels(): ?array
    {
        return $this->localizedAvailableNowLabels;
    }

    /**
     * @return string[]|null
     */
    public function getLocalizedAvailableLaterLabels(): ?array
    {
        return $this->localizedAvailableLaterLabels;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getAvailableDate(): ?DateTimeInterface
    {
        return $this->availableDate;
    }
}
