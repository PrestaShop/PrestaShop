<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
