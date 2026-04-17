<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\State;

class StateSettings
{
    /**
     * Maximum length for state name
     */
    public const MAX_NAME_LENGTH = 64;

    /**
     * Maximum length for iso code (value is constrained by database)
     */
    public const MAX_ISO_CODE_LENGTH = 7;

    /**
     * State iso code field value regexp validation pattern
     */
    public const STATE_ISO_CODE_PATTERN = '/^[a-zA-Z0-9]{1,4}((-)[a-zA-Z0-9]{1,4})?$/';
}
