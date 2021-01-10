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

namespace PrestaShop\PrestaShop\Core\ImageType;

/**
 * Defines contract for ImageGenerator
 */
interface ImageGeneratorInterface
{
    /**
     * @param string $dir
     * @param array $type
     * @param bool $product
     */
    public function deleteOldImages(string $dir, array $type, bool $product = false): void;

    /**
     * @param string $dir
     * @param array $type
     * @param bool $productsImages
     *
     * @return bool|array|string
     */
    public function regenerateNewImages(string $dir, array $type, bool $productsImages = false);

    /**
     * @param string $dir
     * @param array $type
     * @param array $languages
     *
     * @return bool
     */
    public function regenerateNoPictureImages(string $dir, array $type, array $languages): bool;

    /**
     * @param string $dir
     * @param array|null $type
     *
     * @return string|null
     */
    public function regenerateWatermark(string $dir, ?array $type = null): ?string;

    /**
     * @param array $data
     *
     * @return array
     */
    public function regenerateThumbnails(array $data): array;
}
