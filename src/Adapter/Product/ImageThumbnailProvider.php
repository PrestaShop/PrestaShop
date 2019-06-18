<?php

namespace PrestaShop\PrestaShop\Adapter\Product;

use HelperList;
use Image;
use ImageManager;
use PrestaShop\PrestaShop\Core\Image\ImageProviderInterface;
use PrestaShop\PrestaShop\Core\Image\Parser\ImageTagSourceParserInterface;

final class ImageThumbnailProvider implements ImageProviderInterface
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
    public function getPath($imageId)
    {
        $imageType = 'jpg';

        $parentDirectory = _PS_IMG_DIR_ . 'p';

        $image = new Image($imageId);

        $pathToImage = $parentDirectory . '/' . $image->getExistingImgPath() . '.' . $imageType;

        $imageTag = ImageManager::thumbnail(
            $pathToImage,
            'product_mini_' . $imageId . '.' . $imageType,
            HelperList::LIST_THUMBNAIL_SIZE,
            $imageType
        );

        return $this->imageTagSourceParser->parse($imageTag);
    }
}
