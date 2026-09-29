<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\GridView\Command;

use PrestaShop\PrestaShop\Core\Domain\GridView\Exception\GridViewConstraintException;

/**
 * Saves the current grid filters of the employee as a new grid view.
 */
class AddGridViewCommand extends AbstractGridViewCommand
{
    private readonly string $name;

    private readonly string $gridId;

    private readonly string $filterId;

    /**
     * @param string $gridId
     * @param string $name
     * @param bool $shared
     * @param string $controllerRoute
     * @param string $filterId
     * @param string|null $gridState JSON-encoded grid state captured client-side
     * @param array $dynamicDateRules
     *
     * @throws GridViewConstraintException
     */
    public function __construct(
        string $gridId,
        string $name,
        private readonly bool $shared,
        private readonly string $controllerRoute,
        string $filterId,
        private readonly ?string $gridState = null,
        private readonly array $dynamicDateRules = [],
    ) {
        $this->gridId = $this->assertValidGridId($gridId);
        $this->name = $this->assertValidName($name);
        $this->filterId = $this->assertFilterIdBelongsToGrid($this->gridId, $filterId);
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
     * @return string
     */
    public function getControllerRoute(): string
    {
        return $this->controllerRoute;
    }

    /**
     * @return string
     */
    public function getFilterId(): string
    {
        return $this->filterId;
    }

    /**
     * @return string|null
     */
    public function getGridState(): ?string
    {
        return $this->gridState;
    }

    /**
     * @return array
     */
    public function getDynamicDateRules(): array
    {
        return $this->dynamicDateRules;
    }
}
