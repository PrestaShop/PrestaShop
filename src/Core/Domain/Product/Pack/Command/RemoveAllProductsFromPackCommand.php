<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Pack\Command;

use PrestaShop\PrestaShop\Core\Domain\Product\Pack\ValueObject\PackId;

/**
 * Removes all products from provided pack
 */
class RemoveAllProductsFromPackCommand
{
    /**
     * @var PackId
     */
    private $packId;

    /**
     * @param int $packId
     */
    public function __construct(int $packId)
    {
        $this->packId = new PackId($packId);
    }

    /**
     * @return PackId
     */
    public function getPackId(): PackId
    {
        return $this->packId;
    }
}
