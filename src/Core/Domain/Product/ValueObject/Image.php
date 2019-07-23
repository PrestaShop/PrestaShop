<?php

namespace PrestaShop\PrestaShop\Core\Domain\Product\ValueObject;

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
