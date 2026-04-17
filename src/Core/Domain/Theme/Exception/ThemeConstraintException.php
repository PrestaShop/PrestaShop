<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Theme\Exception;

/**
 * Thrown when theme constraints are violated
 */
class ThemeConstraintException extends ThemeException
{
    /**
     * When trying to change theme in multi-shop context
     */
    public const RESTRICTED_ONLY_FOR_SINGLE_SHOP = 1;

    /**
     * When trying to upload zip file which does not contain theme.yml configuration file.
     */
    public const MISSING_CONFIGURATION_FILE = 2;

    /**
     * Its either theme has missing required files or some required properties in theme.yml
     */
    public const INVALID_CONFIGURATION = 3;

    /**
     * Some mandatory files are missing.
     */
    public const INVALID_DATA = 4;
}
