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

namespace PrestaShop\PrestaShop\Core\Domain\Product\Image\Command;

use PrestaShop\PrestaShop\Core\Domain\Product\Image\ValueObject\ImageId;

class UpdateProductImageCommand
{
    /**
     * @var ImageId
     */
    private $imageId;

    /**
     * @var string|null
     */
    private $filePath;

    /**
     * @var bool|null
     */
    private $isCover;

    /**
     * @var array<int, string>|null
     */
    private $localizedLegends;

    /**
     * @var int|null
     */
    private $position;

    /**
     * @param int $imageId
     */
    public function __construct(int $imageId)
    {
        $this->imageId = new ImageId($imageId);
    }

    /**
     * @return ImageId
     */
    public function getImageId(): ImageId
    {
        return $this->imageId;
    }

    /**
     * @return string|null
     */
    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    /**
     * @param string|null $filePath
     *
     * @return self
     */
    public function setFilePath(?string $filePath): self
    {
        $this->filePath = $filePath;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function isCover(): ?bool
    {
        return $this->isCover;
    }

    /**
     * @param bool|null $isCover
     *
     * @return self
     */
    public function setIsCover(?bool $isCover): self
    {
        $this->isCover = $isCover;

        return $this;
    }

    /**
     * @return array<int, string>|null
     */
    public function getLocalizedLegends(): ?array
    {
        return $this->localizedLegends;
    }

    /**
     * @param array<int, string>|null $localizedLegends
     *
     * @return self
     */
    public function setLocalizedLegends(?array $localizedLegends): self
    {
        $this->localizedLegends = $localizedLegends;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getPosition(): ?int
    {
        return $this->position;
    }

    /**
     * @param int|null $position
     *
     * @return self
     */
    public function setPosition(?int $position): self
    {
        $this->position = $position;

        return $this;
    }
}
