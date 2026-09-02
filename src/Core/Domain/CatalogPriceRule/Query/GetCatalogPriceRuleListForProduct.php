<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\Query;

use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

class GetCatalogPriceRuleListForProduct
{
    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @var LanguageId
     */
    private $langId;

    /**
     * @var int|null
     */
    private $limit;

    /**
     * @var int|null
     */
    private $offset;

    /**
     * GetCatalogPriceRuleListForProduct constructor.
     *
     * @param int $productId
     * @param int $langId
     * @param int|null $limit
     * @param int|null $offset
     *
     * @throws ProductConstraintException
     */
    public function __construct(int $productId, int $langId, ?int $limit = null, ?int $offset = null)
    {
        $this->productId = new ProductId($productId);
        $this->langId = new LanguageId($langId);
        $this->limit = $limit;
        $this->offset = $offset;
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
    public function getLangId(): LanguageId
    {
        return $this->langId;
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
}
