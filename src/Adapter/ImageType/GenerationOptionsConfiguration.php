<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

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
            'hight_dpi' => $this->configuration->getBoolean('PS_HIGHT_DPI'),
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

            // Validate them is here as these may be not shown every time
            if (isset($configuration['image_regeneration_method'])) {
                $this->configuration->set('PS_IMAGE_REGENERATION_METHOD', $configuration['image_regeneration_method']);
            }
            if (isset($configuration['product_picture_max_size'])) {
                $this->configuration->set('PS_PRODUCT_PICTURE_MAX_SIZE', $configuration['product_picture_max_size']);
            }
            if (isset($configuration['product_picture_width'])) {
                $this->configuration->set('PS_PRODUCT_PICTURE_WIDTH', $configuration['product_picture_width']);
            }
            if (isset($configuration['product_picture_height'])) {
                $this->configuration->set('PS_PRODUCT_PICTURE_HEIGHT', $configuration['product_picture_height']);
            }
            if (isset($configuration['hight_dpi'])) {
                $this->configuration->set('PS_HIGHT_DPI', $configuration['hight_dpi']);
            }
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
            $configuration['png_quality']
        );
    }
}
