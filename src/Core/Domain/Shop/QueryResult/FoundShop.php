<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Shop\QueryResult;

/**
 * Holds data of a shop found via a SearchShop query
 */
class FoundShop
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
     * @var int
     */
    private $groupId;

    /**
     * @var string
     */
    private $groupName;

    /**
     * @var string
     */
    private $groupColor;

    /**
     * FoundShop constructor.
     *
     * @param int $id
     * @param string $color
     * @param string $name
     * @param int $groupId
     * @param string $groupName
     */
    public function __construct(
        int $id,
        string $color,
        string $name,
        int $groupId,
        string $groupName,
        string $groupColor
    ) {
        $this->id = $id;
        $this->color = $color;
        $this->name = $name;
        $this->groupId = $groupId;
        $this->groupName = $groupName;
        $this->groupColor = $groupColor;
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

    public function getGroupId(): int
    {
        return $this->groupId;
    }

    public function getGroupName(): string
    {
        return $this->groupName;
    }

    public function getGroupColor(): string
    {
        return $this->groupColor;
    }
}
