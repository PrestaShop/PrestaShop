<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopUrlConstraintException;

final class ShopUrlId
{
    private readonly int $shopUrlId;

    /**
     * @throws ShopUrlConstraintException
     */
    public function __construct(int $shopUrlId)
    {
        if (0 >= $shopUrlId) {
            throw new ShopUrlConstraintException(sprintf('Invalid shop url id "%d"', $shopUrlId), ShopUrlConstraintException::INVALID_ID);
        }

        $this->shopUrlId = $shopUrlId;
    }

    public function getValue(): int
    {
        return $this->shopUrlId;
    }
}
