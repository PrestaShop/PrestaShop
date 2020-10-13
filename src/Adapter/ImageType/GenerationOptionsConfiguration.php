<?php

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\ImageType;

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;

/**
 * Images generation settings available in Design > Image Settings
 */
class GenerationOptionsConfiguration implements DataConfigurationInterface
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration(): array
    {
        return [
            'image_quality' => $this->configuration->get('PS_IMAGE_QUALITY'),
            'jpeg_quality' => $this->configuration->getInt('PS_JPEG_QUALITY'),
            'png_quality' => $this->configuration->getInt('PS_PNG_QUALITY'),
            'image_regeneration_method' => $this->configuration->getInt('PS_IMAGE_REGENERATION_METHOD'),
            'product_picture_max_size' => $this->configuration->getInt('PS_PRODUCT_PICTURE_MAX_SIZE'),
            'product_picture_width' => $this->configuration->getInt('PS_PRODUCT_PICTURE_WIDTH'),
            'product_picture_height' => $this->configuration->getInt('PS_PRODUCT_PICTURE_HEIGHT'),
            'high_dpi' => $this->configuration->getBoolean('PS_HIGH_DPI'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration): array
    {
        if ($this->validateConfiguration($configuration)) {
            $this->configuration->set('PS_IMAGE_QUALITY', $configuration['image_quality']);
            $this->configuration->set('PS_JPEG_QUALITY', $configuration['jpeg_quality']);
            $this->configuration->set('PS_PNG_QUALITY', $configuration['png_quality']);
            $this->configuration->set('PS_IMAGE_REGENERATION_METHOD', $configuration['image_regeneration_method']);
            $this->configuration->set('PS_PRODUCT_PICTURE_MAX_SIZE', $configuration['product_picture_max_size']);
            $this->configuration->set('PS_PRODUCT_PICTURE_WIDTH', $configuration['product_picture_width']);
            $this->configuration->set('PS_PRODUCT_PICTURE_HEIGHT', $configuration['product_picture_height']);
            $this->configuration->set('PS_HIGH_DPI', $configuration['high_dpi']);
        }

        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function validateConfiguration(array $configuration): bool
    {
        return isset(
            $configuration['image_quality'],
            $configuration['jpeg_quality'],
            $configuration['png_quality'],
            $configuration['image_regeneration_method'],
            $configuration['product_picture_max_size'],
            $configuration['product_picture_width'],
            $configuration['product_picture_height'],
            $configuration['high_dpi']
        );
    }
}
