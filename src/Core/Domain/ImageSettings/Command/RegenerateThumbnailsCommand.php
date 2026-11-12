<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\ImageSettings\Command;

use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Exception\RegenerateThumbnailsException;

/**
 * Regenerate thumbnails command
 */
class RegenerateThumbnailsCommand
{
    /**
     * @param int $workerCount Number of processes sharing the product images
     * @param int|null $workerIndex Process handling its share of product images, null to start every worker
     *
     * @throws RegenerateThumbnailsException
     */
    public function __construct(
        private readonly string $image,
        private readonly int $imageTypeId,
        private readonly bool $erasePreviousImages,
        private readonly int $workerCount = 1,
        private readonly ?int $workerIndex = null,
    ) {
        if ($workerCount < 1 || ($workerIndex !== null && ($workerIndex < 0 || $workerIndex >= $workerCount))) {
            throw new RegenerateThumbnailsException(sprintf('Invalid worker %s of %d workers', var_export($workerIndex, true), $workerCount));
        }
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function getImageTypeId(): int
    {
        return $this->imageTypeId;
    }

    public function erasePreviousImages(): bool
    {
        return $this->erasePreviousImages;
    }

    public function getWorkerCount(): int
    {
        return $this->workerCount;
    }

    public function getWorkerIndex(): ?int
    {
        return $this->workerIndex;
    }
}
