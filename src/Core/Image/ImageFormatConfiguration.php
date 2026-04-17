<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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

    public const DEFAULT_IMAGE_FORMAT = 'jpg';

    private $formatsToGenerate = [];

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
        // Return formats from cache
        if (!empty($this->formatsToGenerate)) {
            return $this->formatsToGenerate;
        }

        // We will start with the base format, that will be generated no matter what
        $this->formatsToGenerate = [self::DEFAULT_IMAGE_FORMAT];

        // If it is enabled, we check for configured formats.
        $configuration = $this->configuration->get(self::IMAGE_FORMAT_CONFIGURATION_KEY);
        if (!empty($configuration)) {
            foreach (explode(self::SEPARATOR, $configuration) as $format) {
                if (in_array($format, self::SUPPORTED_FORMATS) && !in_array($format, $this->formatsToGenerate)) {
                    $this->formatsToGenerate[] = $format;
                }
            }
        }

        return $this->formatsToGenerate;
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
