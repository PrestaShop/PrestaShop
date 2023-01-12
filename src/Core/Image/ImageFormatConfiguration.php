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

namespace PrestaShop\PrestaShop\Core\Image;

use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Image\Exception\ImageFormatConfigurationException;

class ImageFormatConfiguration implements ImageFormatConfigurationInterface
{
    private const SEPARATOR = ',';

    public const IMAGE_FORMAT_CONFIGURATION_KEY = 'PS_IMAGE_FORMAT';

    public const SUPPORTED_FORMATS = ['jpg', 'png', 'webp', 'avif'];

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    public function getGenerationFormats(): array
    {
        return explode(self::SEPARATOR, $this->configuration->get(self::IMAGE_FORMAT_CONFIGURATION_KEY));
    }

    public function addGenerationFormat(string $format): void
    {
        if (!in_array($format, self::SUPPORTED_FORMATS)) {
            throw new ImageFormatConfigurationException(sprintf('Image format %s unknown or not supported', $format));
        }

        $formats = $this->getGenerationFormats();
        if (!in_array($format, $formats)) {
            $formats[] = $format;
        }

        $this->configuration->set(self::IMAGE_FORMAT_CONFIGURATION_KEY, implode(self::SEPARATOR, $formats));
    }

    public function setListOfGenerationFormats(array $formatList): void
    {
        foreach ($formatList as $format) {
            if (!in_array($format, self::SUPPORTED_FORMATS)) {
                throw new ImageFormatConfigurationException(sprintf('Image format %s unknown or not supported', $format));
            }
        }

        $this->configuration->set(self::IMAGE_FORMAT_CONFIGURATION_KEY, implode(self::SEPARATOR, $formatList));
    }

    public function isGenerationFormatSet(string $format): bool
    {
        return in_array($format, $this->getGenerationFormats());
    }
}
