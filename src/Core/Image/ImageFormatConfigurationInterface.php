<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Image;

/**
 * Interface ImageFormatConfigurationInterface.
 */
interface ImageFormatConfigurationInterface
{
    /**
     * Get a list of configured image generation formats
     *
     * @return array
     */
    public function getGenerationFormats(): array;

    /**
     * Add a generation format to the list
     *
     * @param string $format ex: "jpg" or "png"
     *
     * @return void
     */
    public function addGenerationFormat(string $format): void;

    /**
     * Set several generation formats at once
     *
     * @param array $formatList ex: ['jpg', 'webp']
     *
     * @return void
     */
    public function setListOfGenerationFormats(array $formatList): void;

    /**
     * Check if a given format is configured
     *
     * @param string $format ex: "jpg" or "png"
     *
     * @return bool
     */
    public function isGenerationFormatSet(string $format): bool;
}
