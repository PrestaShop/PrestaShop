<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Shop\QueryResult;

/**
 * Holds data of a shop group found via a SearchShop query
 */
class FoundShopGroup
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $color;

    /**
     * @var string
     */
    private $name;

    /**
     * FoundShopGroup constructor.
     *
     * @param int $id
     * @param string $color
     * @param string $name
     */
    public function __construct(int $id, string $color, string $name)
    {
        $this->id = $id;
        $this->color = $color;
        $this->name = $name;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
