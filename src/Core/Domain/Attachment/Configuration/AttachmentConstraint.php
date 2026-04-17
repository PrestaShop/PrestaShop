<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Attachment\Configuration;

/**
 * Stores attachment validation configuration values
 */
final class AttachmentConstraint
{
    /**
     * Maximum length for name (value is constrained by database)
     */
    public const MAX_NAME_LENGTH = 255;

    /**
     * Prevents class to be instantiated
     */
    private function __construct()
    {
    }
}
