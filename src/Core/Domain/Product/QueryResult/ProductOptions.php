<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
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
     * @var LocalizedTags[]
     */
    private $localizedTags;

    /**
     * @var string
     */
    private $condition;

    /**
     * @var string
     */
    private $isbn;

    /**
     * @var string
     */
    private $upc;

    /**
     * @var string
     */
    private $ean13;

    /**
     * @var string
     */
    private $mpn;

    /**
     * @var string
     */
    private $reference;

    /**
     * @param string $visibility
     * @param bool $availableForOrder
     * @param bool $onlineOnly
     * @param bool $showPrice
     * @param LocalizedTags[] $localizedTags
     * @param string $condition
     * @param string $isbn
     * @param string $upc
     * @param string $ean13
     * @param string $mpn
     * @param string $reference
     */
    public function __construct(
        string $visibility,
        bool $availableForOrder,
        bool $onlineOnly,
        bool $showPrice,
        array $localizedTags,
        string $condition,
        string $isbn,
        string $upc,
        string $ean13,
        string $mpn,
        string $reference
    ) {
        $this->visibility = $visibility;
        $this->availableForOrder = $availableForOrder;
        $this->onlineOnly = $onlineOnly;
        $this->showPrice = $showPrice;
        $this->localizedTags = $localizedTags;
        $this->condition = $condition;
        $this->isbn = $isbn;
        $this->upc = $upc;
        $this->ean13 = $ean13;
        $this->mpn = $mpn;
        $this->reference = $reference;
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
     * @return LocalizedTags[]
     */
    public function getLocalizedTags(): array
    {
        return $this->localizedTags;
    }

    /**
     * @return string
     */
    public function getCondition(): string
    {
        return $this->condition;
    }

    /**
     * @return string
     */
    public function getIsbn(): string
    {
        return $this->isbn;
    }

    /**
     * @return string
     */
    public function getUpc(): string
    {
        return $this->upc;
    }

    /**
     * @return string
     */
    public function getEan13(): string
    {
        return $this->ean13;
    }

    /**
     * @return string
     */
    public function getMpn(): string
    {
        return $this->mpn;
    }

    /**
     * @return string
     */
    public function getReference(): string
    {
        return $this->reference;
    }
}
