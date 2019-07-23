<?php

namespace PrestaShop\PrestaShop\Core\Domain\Product\ValueObject;

/**
 * Existing image
 */
final class ExistingConfigurableImage implements ConfigurableImageInterface
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
     * @var int
     */
    private $imageId;

    /**
     * @param int $imageId
     * @param int $position
     * @param bool $isCover
     * @param array $localizedCaptions
     */
    public function __construct(
        int $imageId,
        int $position,
        bool $isCover,
        array $localizedCaptions
    ) {
        $this->position = $position;
        $this->isCover = $isCover;
        $this->localizedCaptions = $localizedCaptions;
        $this->imageId = $imageId;
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
    public function getImage(): ?Image
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): ?int
    {
        return $this->imageId;
    }
}
