<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\ImageSettings\Command;

/**
 * Command that edits zone
 */
class EditImageSettingsCommand
{
    private array $formats;
    private string $baseFormat;
    private int $avifQuality;
    private int $jpegQuality;
    private int $pngQuality;
    private int $webpQuality;
    private int $generationMethod;
    private int $pictureMaxSize;
    private int $pictureMaxWidth;
    private int $pictureMaxHeight;

    public function setFormats(array $formats): void
    {
        $this->formats = $formats;
    }

    public function getFormats(): string
    {
        // Always have jpg format in all cases.
        $this->formats[] = 'jpg';
        $this->formats = array_unique($this->formats);

        // Prepare data for database.
        return implode(',', $this->formats);
    }

    public function setBaseFormat(string $baseFormat): void
    {
        $this->baseFormat = $baseFormat;
    }

    public function getBaseFormat(): string
    {
        return $this->baseFormat;
    }

    public function setAvifQuality(int $avifQuality): void
    {
        $this->avifQuality = $avifQuality;
    }

    public function getAvifQuality(): int
    {
        return $this->avifQuality;
    }

    public function setJpegQuality(int $jpegQuality): void
    {
        $this->jpegQuality = $jpegQuality;
    }

    public function getJpegQuality(): int
    {
        return $this->jpegQuality;
    }

    public function setPngQuality(int $pngQuality): void
    {
        $this->pngQuality = $pngQuality;
    }

    public function getPngQuality(): int
    {
        return $this->pngQuality;
    }

    public function setWebpQuality(int $webpQuality): void
    {
        $this->webpQuality = $webpQuality;
    }

    public function getWebpQuality(): int
    {
        return $this->webpQuality;
    }

    public function setGenerationMethod(int $generationMethod): void
    {
        $this->generationMethod = $generationMethod;
    }

    public function getGenerationMethod(): int
    {
        return $this->generationMethod;
    }

    public function setPictureMaxSize(int $pictureMaxSize): void
    {
        $this->pictureMaxSize = $pictureMaxSize;
    }

    public function getPictureMaxSize(): int
    {
        return $this->pictureMaxSize;
    }

    public function setPictureMaxWidth(int $pictureMaxWidth): void
    {
        $this->pictureMaxWidth = $pictureMaxWidth;
    }

    public function getPictureMaxWidth(): int
    {
        return $this->pictureMaxWidth;
    }

    public function setPictureMaxHeight(int $pictureMaxHeight): void
    {
        $this->pictureMaxHeight = $pictureMaxHeight;
    }

    public function getPictureMaxHeight(): int
    {
        return $this->pictureMaxHeight;
    }
}
