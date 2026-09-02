<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
