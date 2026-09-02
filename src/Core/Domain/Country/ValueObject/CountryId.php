<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Country\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryConstraintException;

/**
 * Provides country id value
 */
class CountryId implements CountryIdInterface
{
    /**
     * @var int
     */
    private $id;

    /**
     * @param int $id
     *
     * @throws CountryConstraintException
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
     * @throws CountryConstraintException
     */
    private function assertPositiveInt(int $value)
    {
        if (0 >= $value) {
            throw new CountryConstraintException(sprintf('Invalid country id "%s".', var_export($value, true)), CountryConstraintException::INVALID_ID);
        }
    }
}
