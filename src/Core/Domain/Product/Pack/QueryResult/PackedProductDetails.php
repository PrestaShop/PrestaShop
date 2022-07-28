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

namespace PrestaShop\PrestaShop\Core\Domain\Product\Pack\QueryResult;

/**
 * Holds packed product data
 */
class PackedProductDetails
{
    /**
     * @var int
     */
    protected $productId;

    /**
     * @var string
     */
    protected $productName;

    /**
     * @var int
     */
    protected $quantity;

    /**
     * @var int
     */
    protected $combinationId;

    /**
     * @var string
     */
    protected $reference;

    /**
     * @var string
     */
    protected $imageUrl;

    /**
     * @param int $productId
     * @param int $quantity
     * @param int $combinationId
     * @param string $productName
     * @param string $reference
     * @param string $imageUrl
     */
    public function __construct(
        int $productId,
        int $quantity,
        int $combinationId,
        string $productName,
        string $reference,
        string $imageUrl
    ) {
        $this->productId = $productId;
        $this->quantity = $quantity;
        $this->combinationId = $combinationId;
        $this->productName = $productName;
        $this->reference = $reference;
        $this->imageUrl = $imageUrl;
    }

    /**
     * @return int
     */
    public function getProductId(): int
    {
        return $this->productId;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @return int
     */
    public function getCombinationId(): int
    {
        return $this->combinationId;
    }

    /**
     * @return string
     */
    public function getProductName(): string
    {
        return $this->productName;
    }

    /**
     * @return string
     */
    public function getReference(): string
    {
        return $this->reference;
    }

    /**
     * @return string
     */
    public function getImageUrl(): string
    {
        return $this->imageUrl;
    }
}
