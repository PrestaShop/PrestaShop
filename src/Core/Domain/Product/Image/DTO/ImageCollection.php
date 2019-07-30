<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\Image\DTO;

use PrestaShop\PrestaShop\Core\Domain\Product\Image\ValueObject\DeletedImage;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\ValueObject\ExistingConfigurableImage;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\ValueObject\NewConfigurableImage;

/**
 * Holds all product image state cases.
 */
class ImageCollection
{
    /** @var NewConfigurableImage[] */
    private $newImages;

    /** @var ExistingConfigurableImage[] */
    private $existingImages;

    /** @var DeletedImage[] */
    private $deletesImages;

    /**
     * @return NewConfigurableImage[]
     */
    public function getNewImages(): array
    {
        return $this->newImages;
    }

    /**
     * @param NewConfigurableImage[] $newImages
     *
     * @return self
     */
    public function setNewImages(array $newImages): self
    {
        $this->newImages = $newImages;

        return $this;
    }

    /**
     * @return ExistingConfigurableImage[]
     */
    public function getExistingImages(): array
    {
        return $this->existingImages;
    }

    /**
     * @param ExistingConfigurableImage[] $existingImages
     *
     * @return self
     */
    public function setExistingImages(array $existingImages): self
    {
        $this->existingImages = $existingImages;

        return $this;
    }

    /**
     * @return DeletedImage[]
     */
    public function getDeletesImages(): array
    {
        return $this->deletesImages;
    }

    /**
     * @param DeletedImage[] $deletesImages
     *
     * @return self
     */
    public function setDeletesImages(array $deletesImages): self
    {
        $this->deletesImages = $deletesImages;

        return $this;
    }
}
