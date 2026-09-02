<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Hook\Exception;

class HookConstraintException extends HookException
{
    /**
     * Code is used when invalid id is supplied.
     */
    public const INVALID_ID = 1;
}
