<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\ImageSettings\Command;

use PrestaShop\PrestaShop\Core\Domain\ImageSettings\ValueObject\ImageTypeId;

/**
 * Deletes image types on bulk action
 */
class BulkDeleteImageTypeCommand
{
    /**
     * @var array<int, ImageTypeId>
     */
    private array $imageTypeIds;

    /**
     * @param array<int, int> $imageTypeIds
     */
    public function __construct(array $imageTypeIds)
    {
        $this->setImageTypeIds($imageTypeIds);
    }

    /**
     * @return array<int, ImageTypeId>
     */
    public function getImageTypeIds(): array
    {
        return $this->imageTypeIds;
    }

    /**
     * @param array<int, int> $imageTypeIds
     */
    private function setImageTypeIds(array $imageTypeIds): void
    {
        foreach ($imageTypeIds as $imageTypeId) {
            $this->imageTypeIds[] = new ImageTypeId((int) $imageTypeId);
        }
    }
}
