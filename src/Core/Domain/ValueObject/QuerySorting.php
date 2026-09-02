<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Exception\InvalidSortingException;

/**
 * Class QuerySorting is responsible for providing valid sorting parameter.
 */
class QuerySorting
{
    public const ASC = 'ASC';
    public const DESC = 'DESC';

    /**
     * @var string
     */
    private $sorting;

    /**
     * @param string $sorting
     *
     * @throws InvalidSortingException
     */
    public function __construct(string $sorting)
    {
        $sorting = strtoupper($sorting);
        $this->assertSortingSupported($sorting);

        $this->sorting = $sorting;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->sorting;
    }

    /**
     * @param string $sorting
     *
     * @throws InvalidSortingException
     */
    private function assertSortingSupported(string $sorting): void
    {
        if (!in_array($sorting, [self::ASC, self::DESC], true)) {
            throw new InvalidSortingException();
        }
    }
}
