<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Supplier;

use PrestaShop\PrestaShop\Adapter\ImageManager;
use PrestaShop\PrestaShop\Core\Image\ImageProviderInterface;
use PrestaShop\PrestaShop\Core\Image\Parser\ImageTagSourceParserInterface;

/**
 * Class SupplierLogoThumbnailProvider is responsible for providing thumbnail path for supplier logo image.
 */
final class SupplierLogoThumbnailProvider implements ImageProviderInterface
{
    /**
     * @var ImageTagSourceParserInterface
     */
    private $imageTagSourceParser;

    /**
     * @var ImageManager
     */
    private $imageManager;

    /**
     * @param ImageTagSourceParserInterface $imageTagSourceParser
     * @param ImageManager $imageManager
     */
    public function __construct(
        ImageTagSourceParserInterface $imageTagSourceParser,
        ImageManager $imageManager
    ) {
        $this->imageTagSourceParser = $imageTagSourceParser;
        $this->imageManager = $imageManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath($supplierId)
    {
        $imageTag = $this->imageManager->getThumbnailForListing(
            $supplierId,
            'jpg',
            'supplier',
            _PS_SUPP_IMG_DIR_
        );

        return $this->imageTagSourceParser->parse($imageTag);
    }
}
