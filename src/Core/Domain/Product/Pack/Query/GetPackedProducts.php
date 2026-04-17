<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Pack\Query;

use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Product\Pack\ValueObject\PackId;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\InvalidShopConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/**
 * Retrieves product from a pack
 */
class GetPackedProducts
{
    /**
     * @var PackId
     */
    private $packId;

    /**
     * @var LanguageId
     */
    protected $languageId;

    /**
     * @var ShopConstraint
     */
    private $shopConstraint;

    public function __construct(int $packId, int $languageId, ShopConstraint $shopConstraint)
    {
        $this->assertShopConstraintIsSupported($shopConstraint);
        $this->packId = new PackId($packId);
        $this->languageId = new LanguageId($languageId);
        $this->shopConstraint = $shopConstraint;
    }

    /**
     * @return PackId
     */
    public function getPackId(): PackId
    {
        return $this->packId;
    }

    /**
     * @return LanguageId
     */
    public function getLanguageId(): LanguageId
    {
        return $this->languageId;
    }

    /**
     * @return ShopConstraint
     */
    public function getShopConstraint(): ShopConstraint
    {
        return $this->shopConstraint;
    }

    /**
     * @param ShopConstraint $shopConstraint
     */
    private function assertShopConstraintIsSupported(ShopConstraint $shopConstraint): void
    {
        if ($shopConstraint->getShopId()) {
            return;
        }

        throw new InvalidShopConstraintException('Only single shop constraint is supported');
    }
}
