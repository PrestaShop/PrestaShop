<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Category\QueryResult;

class CategoryForTree
{
    /**
     * @var int
     */
    private $categoryId;

    /**
     * @var bool
     */
    private $active;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $displayName;

    /**
     * @var CategoryForTree[]
     */
    private $children;

    /**
     * @param int $categoryId
     * @param bool $active
     * @param string $name,
     * @param string $displayName
     * @param CategoryForTree[] $children
     */
    public function __construct(
        int $categoryId,
        bool $active,
        string $name,
        string $displayName,
        array $children
    ) {
        $this->categoryId = $categoryId;
        $this->active = $active;
        $this->name = $name;
        $this->displayName = $displayName;
        $this->children = $children;
    }

    /**
     * @return int
     */
    public function getCategoryId(): int
    {
        return $this->categoryId;
    }

    /**
     * @return bool
     */
    public function getActive(): bool
    {
        return $this->active;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    /**
     * @return CategoryForTree[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }
}
