<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Customer\Group\ValueObject;

/**
 * Defines contract for group identification value.
 * This interface allows to explicitly define whether group relation is optional or required.
 *
 * @see GroupId
 * @see NoGroupId
 */
interface GroupIdInterface
{
    /**
     * @return int
     */
    public function getValue(): int;
}
