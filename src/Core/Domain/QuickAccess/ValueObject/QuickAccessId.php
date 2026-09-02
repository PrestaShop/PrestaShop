<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\QuickAccess\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\QuickAccess\Exception\QuickAccessConstraintException;

class QuickAccessId
{
    private int $id;

    /**
     * @throws QuickAccessConstraintException
     */
    public function __construct(int $id)
    {
        if ($id <= 0) {
            throw new QuickAccessConstraintException(
                sprintf('Invalid quick access id "%s".', var_export($id, true)),
                QuickAccessConstraintException::INVALID_ID
            );
        }
        $this->id = $id;
    }

    public function getValue(): int
    {
        return $this->id;
    }
}
