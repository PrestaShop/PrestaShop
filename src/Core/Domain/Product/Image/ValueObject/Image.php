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

use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Holds products image.
 */
final class Image
{
    public const ALLOWED_MIME_TYPES = [
        'image/gif',
        'image/png',
        'image/jpeg',
        'image/jpg',
    ];

    /**
     * @var UploadedFile
     */
    private $file;

    /**
     * @param UploadedFile $file
     *
     * @throws ProductConstraintException
     */
    public function __construct(
        UploadedFile $file
    ) {
        $this->assertIsValidFile($file);

        $this->file = $file;
    }

    public function getValue(): UploadedFile
    {
        return $this->file;
    }

    /**
     * @param UploadedFile $file
     *
     * @throws ProductConstraintException
     */
    private function assertIsValidFile(UploadedFile $file): void
    {
        if (!in_array($file->getMimeType(), self::ALLOWED_MIME_TYPES, true)) {
            throw new ProductConstraintException(
                sprintf(
                    'Invalid image mime type "%s" detected. Available values are "%s"',
                    $file->getMimeType(),
                    implode(',', self::ALLOWED_MIME_TYPES)
                )
            );
        }
    }
}
