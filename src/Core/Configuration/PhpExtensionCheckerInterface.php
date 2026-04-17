<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Configuration;

/**
 * Interface PhpExtensionCheckerInterface.
 */
interface PhpExtensionCheckerInterface
{
    /**
     * Check if PHP extension is loaded or not.
     *
     * @param string $extension
     *
     * @return bool
     */
    public function loaded($extension);
}
