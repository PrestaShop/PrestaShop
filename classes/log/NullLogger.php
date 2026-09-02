<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

/**
 * NullLogger is an implementation of the PrestaShop logger that logs nothing.
 * It is convenient as a default value for logger instances, this way the code can be
 * executed the same way even if it does nothing.
 */
class NullLogger extends AbstractLogger
{
    protected function logMessage($message, $level)
    {
        // Null logger doesn't log anything
    }
}
