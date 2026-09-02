<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core;

/**
 * EnvironmentInterface is used to store/access environment information like the current
 * environment name or to know if debug mode is enabled.
 */
interface EnvironmentInterface
{
    /**
     * Indicates the current environment (dev|prod|test)
     *
     * @return string
     */
    public function getName();

    /**
     * Indicates if debug mode is enabled
     *
     * @return bool
     */
    public function isDebug();

    /**
     * Indicates the path to the cache directory
     *
     * @return string
     */
    public function getCacheDir();

    /**
     * Indicates the App ID of the kernel.
     *
     * @return string
     */
    public function getAppId(): string;
}
