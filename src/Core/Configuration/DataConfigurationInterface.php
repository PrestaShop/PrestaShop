<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Configuration;

/**
 * Retrieve and Manage configuration (used to manage forms in "Configure" section of back office).
 */
interface DataConfigurationInterface
{
    /**
     * @return array
     */
    public function getConfiguration();

    /**
     * @param array $configuration
     *
     * @return array if not empty, populated by validation errors
     */
    public function updateConfiguration(array $configuration);

    /**
     * Ensure the parameters passed are valid.
     *
     * @param array $configuration
     *
     * @return bool Returns true if no exception are thrown
     */
    public function validateConfiguration(array $configuration);
}
