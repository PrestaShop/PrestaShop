<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Category\Query;

use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;

/**
 * Provides Category tree list where each category holds its child categories
 */
final class GetCategoriesTree
{
    /**
     * @var LanguageId
     */
    private $languageId;

    /**
     * @var ShopId
     */
    private $shopId;

    /**
     * @param int $languageId
     * @param int $shopId
     */
    public function __construct(
        int $languageId,
        int $shopId
    ) {
        $this->languageId = new LanguageId($languageId);
        $this->shopId = new ShopId($shopId);
    }

    /**
     * @return LanguageId
     */
    public function getLanguageId(): LanguageId
    {
        return $this->languageId;
    }

    /**
     * @return ShopId
     */
    public function getShopId(): ShopId
    {
        return $this->shopId;
    }
}
