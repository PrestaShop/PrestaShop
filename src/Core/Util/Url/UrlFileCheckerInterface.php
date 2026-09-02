<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Util\Url;

/**
 * Interface UrlFileCheckerInterface defines contract for UrlFileChecker.
 */
interface UrlFileCheckerInterface
{
    /**
     * Check if .htaccess file for Front Office is writable.
     *
     * @return bool
     */
    public function isHtaccessFileWritable();

    /**
     * Check if robots.txt file for Front Office is writable.
     *
     * @return bool
     */
    public function isRobotsFileWritable();
}
