<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\ImageSettings\Command;

/**
 * Regenerate thumbnails command
 */
class RegenerateThumbnailsCommand
{
    public function __construct(
        private readonly string $image,
        private readonly int $imageTypeId,
        private readonly bool $erasePreviousImages,
    ) {
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
}
