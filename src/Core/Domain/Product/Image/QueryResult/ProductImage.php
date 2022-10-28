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

namespace PrestaShop\PrestaShop\Core\Domain\Product\Image\QueryResult;

/**
 * Transfers product image data
 */
class ProductImage
{
    /**
     * @var int
     */
    private $imageId;

    /**
     * @var bool
     */
    private $cover;

    /**
     * @var int
     */
    private $position;

    /**
     * @var array
     */
    private $localizedLegends;

    /**
     * @var string
     */
    private $imageUrl;

    /**
     * @var string
     */
    private $thumbnailUrl;

    /**
     * @var int[]
     */
    private $shopIds;

    /**
     * @param int $imageId
     * @param bool $cover
     * @param int $position
     * @param array $localizedLegends
     * @param string $imageUrl
     * @param string $thumbnailUrl
     * @param int[] $shopIds
     */
    public function __construct(
        int $imageId,
        bool $cover,
        int $position,
        array $localizedLegends,
        string $imageUrl,
        string $thumbnailUrl,
        array $shopIds
    ) {
        $this->imageId = $imageId;
        $this->cover = $cover;
        $this->position = $position;
        $this->localizedLegends = $localizedLegends;
        $this->imageUrl = $imageUrl;
        $this->thumbnailUrl = $thumbnailUrl;
        $this->shopIds = $shopIds;
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
        return $this->cover;
    }

    /**
     * @return array
     */
    public function getLocalizedLegends(): array
    {
        return $this->localizedLegends;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @return string
     */
    public function getImageUrl(): string
    {
        return $this->imageUrl;
    }

    /**
     * @return string
     */
    public function getThumbnailUrl(): string
    {
        return $this->thumbnailUrl;
    }

    /**
     * @return int[]
     */
    public function getShopIds(): array
    {
        return $this->shopIds;
    }
}
