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
