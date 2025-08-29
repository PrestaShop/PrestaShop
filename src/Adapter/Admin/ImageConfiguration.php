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

namespace PrestaShop\PrestaShop\Adapter\Admin;

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;

/**
 * Manages the configuration data about image.
 */
class ImageConfiguration implements DataConfigurationInterface
{
    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        return [
            'formats' => $this->configuration->get('PS_IMAGE_FORMAT'),
            'base-format' => $this->configuration->get('PS_IMAGE_QUALITY'),
            'avif-quality' => (int) $this->configuration->get('PS_AVIF_QUALITY'),
            'jpeg-quality' => (int) $this->configuration->get('PS_JPEG_QUALITY'),
            'png-quality' => (int) $this->configuration->get('PS_PNG_QUALITY'),
            'webp-quality' => (int) $this->configuration->get('PS_WEBP_QUALITY'),
            'generation-method' => (int) $this->configuration->get('PS_IMAGE_GENERATION_METHOD'),
            'picture-max-size' => (int) $this->configuration->get('PS_PRODUCT_PICTURE_MAX_SIZE'),
            'picture-max-width' => (int) $this->configuration->get('PS_PRODUCT_PICTURE_WIDTH'),
            'picture-max-height' => (int) $this->configuration->get('PS_PRODUCT_PICTURE_HEIGHT'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        $errors = [];

        if ($this->validateConfiguration($configuration)) {
            $this->configuration->set('PS_IMAGE_FORMAT', $configuration['formats']);
            $this->configuration->set('PS_IMAGE_QUALITY', $configuration['base-format']);
            $this->configuration->set('PS_AVIF_QUALITY', (int) $configuration['avif-quality']);
            $this->configuration->set('PS_JPEG_QUALITY', (int) $configuration['jpeg-quality']);
            $this->configuration->set('PS_PNG_QUALITY', (int) $configuration['png-quality']);
            $this->configuration->set('PS_WEBP_QUALITY', (int) $configuration['webp-quality']);
            $this->configuration->set('PS_IMAGE_GENERATION_METHOD', (int) $configuration['generation-method']);
            $this->configuration->set('PS_PRODUCT_PICTURE_MAX_SIZE', (int) $configuration['picture-max-size']);
            $this->configuration->set('PS_PRODUCT_PICTURE_WIDTH', (int) $configuration['picture-max-width']);
            $this->configuration->set('PS_PRODUCT_PICTURE_HEIGHT', (int) $configuration['picture-max-height']);
        }

        return $errors;
    }

    /**
     * {@inheritdoc}
     */
    public function validateConfiguration(array $configuration)
    {
        return isset(
            $configuration['formats'],
            $configuration['base-format'],
            $configuration['avif-quality'],
            $configuration['jpeg-quality'],
            $configuration['png-quality'],
            $configuration['webp-quality'],
            $configuration['generation-method'],
            $configuration['picture-max-size'],
            $configuration['picture-max-width'],
            $configuration['picture-max-height'],
        );
    }
}
