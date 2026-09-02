<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Image\QueryResult\Shop;

class ShopImageAssociation
{
    /**
     * @var int
     */
    private $imageId;

    /**
     * @var bool
     */
    private $isCover;

    public function __construct(
        int $imageId,
        bool $isCover
    ) {
        $this->imageId = $imageId;
        $this->isCover = $isCover;
    }

    /**
     * @return int
     */
    public function getImageId(): int
    {
        return $this->imageId;
    }

    /**
     * @return bool
     */
    public function isCover(): bool
    {
        return $this->isCover;
    }
}
