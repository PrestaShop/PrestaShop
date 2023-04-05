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
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
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
     * @var CacheManager
     */
    private $imageCacheManager;

    /**
     * @param ImageTagSourceParserInterface $imageTagSourceParser
     */
    public function __construct(
        ImageTagSourceParserInterface $imageTagSourceParser,
        CacheManager $imageCacheManager
    ) {
        $this->imageTagSourceParser = $imageTagSourceParser;
        $this->imageCacheManager = $imageCacheManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath($manufacturerId)
    {
        $pathToImage = '/img/m/' . $manufacturerId . '.jpg';
        return $this->imageCacheManager->getBrowserPath($pathToImage, 'my_thumb');
    }
}
