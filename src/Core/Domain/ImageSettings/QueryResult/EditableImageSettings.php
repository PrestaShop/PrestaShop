<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\ImageSettings\QueryResult;

/**
 * Transfers image settings data for editing
 */
class EditableImageSettings
{
    public function __construct(
        private readonly string $formats,
        private readonly string $baseFormat,
        private readonly int $avifQuality,
        private readonly int $jpegQuality,
        private readonly int $pngQuality,
        private readonly int $webpQuality,
        private readonly int $generationMethod,
        private readonly int $pictureMaxSize,
        private readonly int $pictureMaxWidth,
        private readonly int $pictureMaxHeight
    ) {
    }

    public function getFormats(): array
    {
        return array_map('trim', explode(',', $this->formats));
    }

    public function getBaseFormat(): string
    {
        return $this->baseFormat;
    }

    public function getAvifQuality(): int
    {
        return $this->avifQuality;
    }

    public function getJpegQuality(): int
    {
        return $this->jpegQuality;
    }

    public function getPngQuality(): int
    {
        return $this->pngQuality;
    }

    public function getWebpQuality(): int
    {
        return $this->webpQuality;
    }

    public function getGenerationMethod(): int
    {
        return $this->generationMethod;
    }

    public function getPictureMaxSize(): int
    {
        return $this->pictureMaxSize;
    }

    public function getPictureMaxWidth(): int
    {
        return $this->pictureMaxWidth;
    }

    public function getPictureMaxHeight(): int
    {
        return $this->pictureMaxHeight;
    }
}
