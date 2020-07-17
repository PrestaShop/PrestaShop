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

namespace PrestaShop\PrestaShop\Adapter\Manufacturer;

use HelperList;
use ImageManager;
use PrestaShop\PrestaShop\Core\Image\ImageProviderInterface;
use PrestaShop\PrestaShop\Core\Image\Parser\ImageTagSourceParserInterface;

/**
 * Provides path for manufacturer logo thumbnail
 */
final class ManufacturerLogoThumbnailProvider implements ImageProviderInterface
{
    /**
     * @var ImageTagSourceParserInterface
     */
    private $imageTagSourceParser;

    /**
     * @param ImageTagSourceParserInterface $imageTagSourceParser
     */
    public function __construct(
        ImageTagSourceParserInterface $imageTagSourceParser
    ) {
        $this->imageTagSourceParser = $imageTagSourceParser;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath($manufacturerId)
    {
        $pathToImage = _PS_MANU_IMG_DIR_ . $manufacturerId . '.jpg';

        $imageTag = ImageManager::thumbnail(
            $pathToImage,
            'manufacturer_mini_' . $manufacturerId . '.jpg',
            HelperList::LIST_THUMBNAIL_SIZE
        );

        return $this->imageTagSourceParser->parse($imageTag);
    }
}
