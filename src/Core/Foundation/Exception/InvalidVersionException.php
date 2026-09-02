<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Foundation\Exception;

use PrestaShop\PrestaShop\Core\Exception\CoreException;

/**
 * This exception will be thrown if an invalid Shop version name is used
 * in the application.
 */
class InvalidVersionException extends CoreException
{
    /**
     * Construct.
     *
     * @param string $version the provided PrestaShop version
     */
    public function __construct($version)
    {
        $message = sprintf(
            'You provided an invalid version string ("%s"). A valid version string ' .
            'must contain numeric characters separated by "." characters, for example "1.7.4.0".',
            $version
        );
        parent::__construct($message, 0, null);
    }
}
