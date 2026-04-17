<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Title\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Title\Exception\TitleConstraintException;

/**
 * Provides Title id
 */
class TitleId
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @param int $id
     *
     * @throws TitleConstraintException
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
     * @throws TitleConstraintException
     */
    private function assertPositiveInt(int $value)
    {
        if (0 >= $value) {
            throw new TitleConstraintException(sprintf('Invalid title id "%s".', var_export($value, true)), TitleConstraintException::INVALID_ID);
        }
    }
}
