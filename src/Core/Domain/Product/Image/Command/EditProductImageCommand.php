<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Image\Command;

use PrestaShop\PrestaShop\Core\Domain\Product\Image\ValueObject\ImageId;

class EditProductImageCommand
{
    /**
     * @var ImageId
     */
    private $imageId;

    /**
     * @var string[]|null
     */
    private $localizedLegends;

    /**
     * @var bool|null
     */
    private $cover;

    /**
     * @param int $imageId
     */
    public function __construct(
        int $imageId
    ) {
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
     * @return string[]|null
     */
    public function getLocalizedLegends(): ?array
    {
        return $this->localizedLegends;
    }

    /**
     * @param string[]|null $localizedLegends
     *
     * @return EditProductImageCommand
     */
    public function setLocalizedLegends(?array $localizedLegends): EditProductImageCommand
    {
        $this->localizedLegends = $localizedLegends;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function isCover(): ?bool
    {
        return $this->cover;
    }

    /**
     * @param bool|null $cover
     *
     * @return EditProductImageCommand
     */
    public function setCover(?bool $cover): EditProductImageCommand
    {
        $this->cover = $cover;

        return $this;
    }
}
