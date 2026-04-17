<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Tab\ValueObject;

class AllTab implements TabIdInterface
{
    public const ALL_TAB_ID = -1;

    /**
     * @return int
     */
    public function getValue(): int
    {
        return static::ALL_TAB_ID;
    }
}
