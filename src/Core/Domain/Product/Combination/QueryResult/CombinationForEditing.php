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

namespace PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult;

/**
 * Transfers combination data for editing
 */
class CombinationForEditing
{
    /**
     * @var int
     */
    private $combinationId;

    /**
     * @var int
     */
    private $productId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var CombinationDetails
     */
    private $details;

    /**
     * @var CombinationPrices
     */
    private $prices;

    /**
     * @var CombinationStock
     */
    private $stock;

    /**
     * @var int[]
     */
    private $imageIds;

    /**
     * @param int $combinationId
     * @param int $productId
     * @param string $name
     * @param CombinationDetails $options
     * @param CombinationPrices $prices
     * @param CombinationStock $stock
     * @param int[] $imageIds
     */
    public function __construct(
        int $combinationId,
        int $productId,
        string $name,
        CombinationDetails $options,
        CombinationPrices $prices,
        CombinationStock $stock,
        array $imageIds
    ) {
        $this->combinationId = $combinationId;
        $this->productId = $productId;
        $this->name = $name;
        $this->details = $options;
        $this->stock = $stock;
        $this->prices = $prices;
        $this->imageIds = $imageIds;
    }

    /**
     * @return int
     */
    public function getCombinationId(): int
    {
        return $this->combinationId;
    }

    /**
     * @return int
     */
    public function getProductId(): int
    {
        return $this->productId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return CombinationDetails
     */
    public function getDetails(): CombinationDetails
    {
        return $this->details;
    }

    /**
     * @return CombinationPrices
     */
    public function getPrices(): CombinationPrices
    {
        return $this->prices;
    }

    /**
     * @return CombinationStock
     */
    public function getStock(): CombinationStock
    {
        return $this->stock;
    }

    /**
     * @return int[]
     */
    public function getImageIds(): array
    {
        return $this->imageIds;
    }
}
