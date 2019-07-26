<?php

namespace PrestaShop\PrestaShop\Core\Domain\Product\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * New not created image.
 */
final class NewConfigurableImage implements ConfigurableImageInterface, NewImageInterface
{
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
     * @var Image
     */
    private $image;

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
        $this->image = new Image($file);
        $this->position = $position;
        $this->isCover = $isCover;
        $this->localizedCaptions = $localizedCaptions;
    }

    /**
     * {@inheritdoc}
     */
    public function getImage(): Image
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
     * {@inheritdoc}
     */
    public function getId(): ?int
    {
        return null;
    }
}
