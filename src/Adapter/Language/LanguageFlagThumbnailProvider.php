<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Language;

use HelperList;
use ImageManager;
use PrestaShop\PrestaShop\Core\Image\ImageProviderInterface;
use PrestaShop\PrestaShop\Core\Image\Parser\ImageTagSourceParserInterface;

/**
 * Class LanguageThumbnailProvider provides path to language's flag thumbnail.
 */
final class LanguageFlagThumbnailProvider implements ImageProviderInterface
{
    /**
     * @var ImageTagSourceParserInterface
     */
    private $imageTagSourceParser;

    /**
     * @var int
     */
    private $contextShopId;

    /**
     * @param ImageTagSourceParserInterface $imageTagSourceParser
     * @param int $contextShopId
     */
    public function __construct(
        ImageTagSourceParserInterface $imageTagSourceParser,
        $contextShopId
    ) {
        $this->imageTagSourceParser = $imageTagSourceParser;
        $this->contextShopId = $contextShopId;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath($languageId)
    {
        $pathToImage = _PS_IMG_DIR_ . 'l' . DIRECTORY_SEPARATOR . $languageId . '.jpg';

        $imageTag = ImageManager::thumbnail(
            $pathToImage,
            'lang_mini_' . $languageId . '_' . $this->contextShopId . '.jpg',
            HelperList::LIST_THUMBNAIL_SIZE,
            'jpg'
        );

        return $this->imageTagSourceParser->parse($imageTag);
    }
}
