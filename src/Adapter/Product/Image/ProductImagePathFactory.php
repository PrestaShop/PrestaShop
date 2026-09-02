<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Image;

use PrestaShop\PrestaShop\Core\Domain\Product\Image\ValueObject\ImageId;

class ProductImagePathFactory
{
    public const IMAGE_TYPE_SMALL_DEFAULT = 'small_default';
    public const IMAGE_TYPE_MEDIUM_DEFAULT = 'medium_default';
    public const IMAGE_TYPE_LARGE_DEFAULT = 'large_default';
    public const IMAGE_TYPE_HOME_DEFAULT = 'home_default';
    public const IMAGE_TYPE_CART_DEFAULT = 'cart_default';

    public const DEFAULT_IMAGE_FORMAT = 'jpg';

    /**
     * @var string
     */
    private $temporaryImgDir;

    /**
     * @var string
     */
    private $pathToBaseDir;

    /**
     * @var string
     */
    private $contextLangIsoCode;

    /**
     * @param string $pathToBaseDir
     * @param string $temporaryImgDir
     * @param string $contextLangIsoCode
     */
    public function __construct(
        string $pathToBaseDir,
        string $temporaryImgDir,
        string $contextLangIsoCode
    ) {
        // make sure one trailing slash is always there
        $this->temporaryImgDir = rtrim($temporaryImgDir, '/') . '/';
        $this->pathToBaseDir = rtrim($pathToBaseDir, '/') . '/';
        $this->contextLangIsoCode = $contextLangIsoCode;
    }

    /**
     * @param ImageId $imageId
     * @param string $extension
     *
     * @return string
     */
    public function getPath(ImageId $imageId, string $extension = self::DEFAULT_IMAGE_FORMAT): string
    {
        $path = $this->getBaseImagePathWithoutExtension($imageId);

        return sprintf('%s.%s', $path, $extension);
    }

    /**
     * @param ImageId $imageId
     * @param string $type
     * @param string $extension
     *
     * @return string
     */
    public function getPathByType(ImageId $imageId, string $type, string $extension = self::DEFAULT_IMAGE_FORMAT): string
    {
        $path = $this->getBaseImagePathWithoutExtension($imageId);

        return sprintf('%s-%s.%s', $path, $type, $extension);
    }

    /**
     * @param string $type
     * @param string|null $langIso if null, will use $contextLangIsoCode by default
     *
     * @return string
     */
    public function getNoImagePath(string $type, ?string $langIso = null): string
    {
        if (!$langIso) {
            $langIso = $this->contextLangIsoCode;
        }

        return sprintf('%s%s-%s-%s.jpg', $this->pathToBaseDir, $langIso, 'default', $type);
    }

    /**
     * @param int $productId
     *
     * @return string
     */
    public function getCachedCover(int $productId): string
    {
        return sprintf('%sproduct_%d.jpg', $this->temporaryImgDir, $productId);
    }

    /**
     * @param int $productId
     * @param int $shopId
     *
     * @return string
     */
    public function getHelperThumbnail(int $productId, int $shopId): string
    {
        return sprintf('%sproduct_mini_%d_%d.jpg', $this->temporaryImgDir, $productId, $shopId);
    }

    /**
     * @param ImageId $imageId
     *
     * @return string
     */
    public function getImageFolder(ImageId $imageId): string
    {
        $path = implode('/', str_split((string) $imageId->getValue()));

        return $this->pathToBaseDir . $path;
    }

    /**
     * @param ImageId $imageId
     *
     * @return string
     */
    private function getBaseImagePathWithoutExtension(ImageId $imageId): string
    {
        return $this->getImageFolder($imageId) . '/' . $imageId->getValue();
    }
}
