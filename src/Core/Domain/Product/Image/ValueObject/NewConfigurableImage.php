<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\Image\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Product\Image\Exception\ProductImageConstraintException;
use SplFileObject;
use function in_array;

/**
 * New not created image.
 */
final class NewConfigurableImage implements ConfigurableImageInterface
{
    public const ALLOWED_MIME_TYPES = [
        'image/gif',
        'image/png',
        'image/jpeg',
        'image/jpg',
    ];

    /**
     * @var int
     */
    private $position;

    /**
     * @var bool
     */
    private $isCover;

    /**
     * @var array
     */
    private $localizedCaptions;

    /**
     * @var SplFileObject
     */
    private $image;

    /**
     * @param string $filePath
     * @param int $position
     * @param bool $isCover
     * @param array $localizedCaptions
     *
     * @throws ProductImageConstraintException
     */
    public function __construct(
        string $filePath,
        int $position,
        bool $isCover,
        array $localizedCaptions
    ) {
        $file = new SplFileObject($filePath);

        $this->assertIsValidImage($file);

        $this->image = $file;
        $this->position = $position;
        $this->isCover = $isCover;
        $this->localizedCaptions = $localizedCaptions;
    }

    /**
     * {@inheritdoc}
     */
    public function getImage(): SplFileObject
    {
        return $this->image;
    }

    /**
     * {@inheritdoc}
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * {@inheritdoc}
     */
    public function isCover(): bool
    {
        return $this->isCover;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocalizedCaptions(): array
    {
        return $this->localizedCaptions;
    }

    /**
     * @param SplFileObject $file
     *
     * @throws ProductImageConstraintException
     */
    private function assertIsValidImage(SplFileObject $file): void
    {
        if (!$file->isFile()) {
            throw new ProductImageConstraintException(
                sprintf(
                    'File "%s" is not a regular file',
                    $file->getPathname()
                ),
                ProductImageConstraintException::INVALID_FILE
            );
        }

        $mimeType = mime_content_type($file->getPathname());

        if (!in_array($mimeType, self::ALLOWED_MIME_TYPES, true)) {
            throw new ProductImageConstraintException(
                sprintf(
                    'Invalid image mime type "%s" detected. Available values are "%s"',
                    $mimeType,
                    implode(',', self::ALLOWED_MIME_TYPES)
                ),
                ProductImageConstraintException::INVALID_MIME_TYPE
            );
        }
    }
}
