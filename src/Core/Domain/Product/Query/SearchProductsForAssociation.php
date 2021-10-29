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

namespace PrestaShop\PrestaShop\Core\Domain\Product\Query;

use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;

class SearchProductsForAssociation
{
    /**
     * This is the minimum length of search phrase
     */
    const SEARCH_PHRASE_MIN_LENGTH = 3;

    /**
     * @var string
     */
    private $phrase;

    /**
     * @var LanguageId
     */
    private $languageId;

    /**
     * @var ShopId
     */
    private $shopId;

    /**
     * @var int|null
     */
    private $limit;

    /**
     * @param string $phrase
     * @param int $languageId
     * @param int $shopId
     * @param int|null $limit
     */
    public function __construct(string $phrase, int $languageId, int $shopId, ?int $limit = null)
    {
        if (null !== $limit && $limit <= 0) {
            throw new ProductConstraintException('Search limit must be a positive integer or null', ProductConstraintException::INVALID_SEARCH_LIMIT);
        }
        if (strlen($phrase) < static::SEARCH_PHRASE_MIN_LENGTH) {
            throw new ProductConstraintException(sprintf(
                'Search phase must have a minimum length of %d characters.',
                static::SEARCH_PHRASE_MIN_LENGTH
            ), ProductConstraintException::INVALID_SEARCH_PHRASE_LENGTH);
        }

        $this->phrase = $phrase;
        $this->limit = $limit;
        $this->shopId = new ShopId($shopId);
        $this->languageId = new LanguageId($languageId);
    }

    /**
     * @return string
     */
    public function getPhrase(): string
    {
        return $this->phrase;
    }

    /**
     * @return ShopId
     */
    public function getShopId(): ShopId
    {
        return $this->shopId;
    }

    /**
     * @return LanguageId
     */
    public function getLanguageId(): LanguageId
    {
        return $this->languageId;
    }

    /**
     * @return int|null
     */
    public function getLimit(): ?int
    {
        return $this->limit;
    }
}
