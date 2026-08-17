<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\GridView\QueryResult;

/**
 * Grid view data transferred to the edit form.
 */
class EditableGridView
{
    /**
     * @param int $gridViewId
     * @param string $gridId
     * @param string $name
     * @param bool $shared
     * @param array $dynamicDateRules
     * @param array<string, array{id: string, name: string}> $dateRangeFilterFields date-range filters stored
     *                                                                              in the view, indexed by field id
     */
    public function __construct(
        private readonly int $gridViewId,
        private readonly string $gridId,
        private readonly string $name,
        private readonly bool $shared,
        private readonly array $dynamicDateRules,
        private readonly array $dateRangeFilterFields,
    ) {
    }

    /**
     * @return int
     */
    public function getGridViewId(): int
    {
        return $this->gridViewId;
    }

    /**
     * @return string
     */
    public function getGridId(): string
    {
        return $this->gridId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isShared(): bool
    {
        return $this->shared;
    }

    /**
     * @return array
     */
    public function getDynamicDateRules(): array
    {
        return $this->dynamicDateRules;
    }

    /**
     * @return array<string, array{id: string, name: string}>
     */
    public function getDateRangeFilterFields(): array
    {
        return $this->dateRangeFilterFields;
    }
}
