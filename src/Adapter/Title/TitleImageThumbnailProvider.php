<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Title;

use PrestaShop\PrestaShop\Adapter\ImageManager;
use PrestaShop\PrestaShop\Core\Image\ImageProviderInterface;
use PrestaShop\PrestaShop\Core\Image\Parser\ImageTagSourceParserInterface;

/**
 * Class TitleImageThumbnailProvider provides path to title's image thumbnail and generate it.
 */
class TitleImageThumbnailProvider implements ImageProviderInterface
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
     * @var string
     */
    private $genderImageDirectoryName;

    /**
     * @param ImageTagSourceParserInterface $imageTagSourceParser
     * @param ImageManager $imageManager
     * @param string $genderImageDirectoryName
     */
    public function __construct(
        ImageTagSourceParserInterface $imageTagSourceParser,
        ImageManager $imageManager,
        string $genderImageDirectoryName
    ) {
        $this->imageTagSourceParser = $imageTagSourceParser;
        $this->imageManager = $imageManager;
        $this->genderImageDirectoryName = $genderImageDirectoryName;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath($titleId): ?string
    {
        $imageTag = $this->imageManager->getThumbnailForListing(
            $titleId,
            'jpg',
            'genders',
            $this->genderImageDirectoryName
        );

        return $this->imageTagSourceParser->parse($imageTag);
    }
}
