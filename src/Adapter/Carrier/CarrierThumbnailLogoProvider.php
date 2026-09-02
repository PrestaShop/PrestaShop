<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Carrier;

use HelperList;
use ImageManager;
use PrestaShop\PrestaShop\Core\Image\ImageProviderInterface;
use PrestaShop\PrestaShop\Core\Image\Parser\ImageTagSourceParserInterface;

/**
 * Provides path for Carrier logo in carriers grid
 */
class CarrierThumbnailLogoProvider implements ImageProviderInterface
{
    /**
     * @var ImageTagSourceParserInterface
     */
    private $imageTagSourceParser;

    /**
     * @param ImageTagSourceParserInterface $parser
     */
    public function __construct(ImageTagSourceParserInterface $parser)
    {
        $this->imageTagSourceParser = $parser;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath($carrierId)
    {
        $pathToImage = _PS_SHIP_IMG_DIR_ . $carrierId . '.jpg';

        $imageTag = ImageManager::thumbnail(
            $pathToImage,
            'carrier_mini_' . $carrierId . '.jpg',
            HelperList::LIST_THUMBNAIL_SIZE
        );

        return $this->imageTagSourceParser->parse($imageTag);
    }
}
