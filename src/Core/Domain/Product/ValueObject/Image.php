<?php

namespace PrestaShop\PrestaShop\Core\Domain\Product\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class Image
{
    public const ALLOWED_MIME_TYPES = [
        'image/gif',
        'image/png',
        'image/jpeg', //todo: webp?
    ];

    /**
     * @var ImageId
     */
    private $imageId;

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
     * @var UploadedFile
     */
    private $file;

    /**
     * @param UploadedFile $file
     * @param int $position
     * @param bool $isCover
     * @param array $localizedCaptions
     *
     * @throws ProductConstraintException
     */
    public function __construct(
        UploadedFile $file,
        int $position,
        bool $isCover,
        array $localizedCaptions
    ) {
        $this->assertIsValidFile($file);

        $this->position = $position;
        $this->isCover = $isCover;
        $this->localizedCaptions = $localizedCaptions;
        $this->file = $file;
    }

    /**
     * @param int $imageId
     *
     * @return self
     */
    public function setImageId(int $imageId): self
    {
        $this->imageId = new ImageId($imageId);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getImageId()
    {
        return $this->imageId;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @return bool
     */
    public function isCover(): bool
    {
        return $this->isCover;
    }

    /**
     * @return array
     */
    public function getLocalizedCaptions(): array
    {
        return $this->localizedCaptions;
    }

    /**
     * @return UploadedFile
     */
    public function getFile(): UploadedFile
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
                    self::ALLOWED_MIME_TYPES
                )
            );
        }
    }
}
