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

namespace PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Query;

use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * Retrieves product specific prices
 */
class GetSpecificPriceList
{
    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @var LanguageId
     */
    private $languageId;

    /**
     * @var int|null
     */
    private $limit;

    /**
     * @var int|null
     */
    private $offset;

    /**
     * @var array<string, mixed>
     */
    private $filters;

    /**
     * @param int $productId
     * @param int $languageId
     * @param int|null $limit
     * @param int|null $offset
     * @param array<string, mixed> $filters
     */
    public function __construct(
        int $productId,
        int $languageId,
        ?int $limit = null,
        ?int $offset = null,
        array $filters = []
    ) {
        $this->productId = new ProductId($productId);
        $this->languageId = new LanguageId($languageId);
        $this->limit = $limit;
        $this->offset = $offset;
        $this->filters = $filters;
    }

    /**
     * @return ProductId
     */
    public function getProductId(): ProductId
    {
        return $this->productId;
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

    /**
     * @return int|null
     */
    public function getOffset(): ?int
    {
        return $this->offset;
    }

    /**
     * @return array<string, mixed>
     */
    public function getFilters(): array
    {
        return $this->filters;
    }
}
