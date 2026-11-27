<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Customer\Group\Exception;

class CannotDeleteGroupException extends GroupException
{
    /**
     * The group is configured as the visitors, guests or customers default group
     */
    public const GROUP_USED_AS_DEFAULT = 10;
}
