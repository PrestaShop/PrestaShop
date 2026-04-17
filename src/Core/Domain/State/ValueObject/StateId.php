<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\State\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\State\Exception\StateConstraintException;

/**
 * Provides state id
 */
class StateId implements StateIdInterface
{
    /**
     * @var int
     */
    private $id;

    /**
     * @param int $id
     *
     * @throws StateConstraintException
     */
    public function __construct(int $id)
    {
        $this->assertPositiveInt($id);
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->id;
    }

    /**
     * @param int $value
     *
     * @throws StateConstraintException
     */
    private function assertPositiveInt(int $value)
    {
        if (0 >= $value) {
            throw new StateConstraintException(sprintf('Invalid state id "%s".', var_export($value, true)), StateConstraintException::INVALID_ID);
        }
    }
}
