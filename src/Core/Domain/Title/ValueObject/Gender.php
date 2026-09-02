<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Title\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Title\Exception\TitleConstraintException;

class Gender
{
    public const TYPE_MALE = 0;
    public const TYPE_FEMALE = 1;
    public const TYPE_OTHER = 2;

    /**
     * @var int
     */
    protected $type;

    /**
     * @param int $gender
     *
     * @throws TitleConstraintException
     */
    public function __construct(int $gender)
    {
        $this->assertIsAuthValues($gender);
        $this->type = $gender;
    }

    /**
     * @param int $gender
     *
     * @return void
     *
     * @throws TitleConstraintException
     */
    protected function assertIsAuthValues(int $gender): void
    {
        if (!in_array($gender, [
            self::TYPE_MALE,
            self::TYPE_FEMALE,
            self::TYPE_OTHER,
        ])) {
            throw new TitleConstraintException(sprintf('Invalid type : "%d".', $gender), TitleConstraintException::INVALID_TYPE);
        }
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->type;
    }
}
