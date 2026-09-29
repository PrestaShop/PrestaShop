<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\GridView\Command;

use PrestaShop\PrestaShop\Core\Domain\GridView\Exception\GridViewConstraintException;

/**
 * Creates or updates the per-employee configuration of a grid views panel.
 */
class SaveGridConfigurationCommand extends AbstractGridViewCommand
{
    private readonly string $gridId;

    private readonly string $filterId;

    /**
     * @param string $gridId
     * @param string $controllerRoute
     * @param string $filterId
     * @param bool $displaySharedFilters
     * @param bool $displayTotals
     *
     * @throws GridViewConstraintException
     */
    public function __construct(
        string $gridId,
        private readonly string $controllerRoute,
        string $filterId,
        private readonly bool $displaySharedFilters,
        private readonly bool $displayTotals,
    ) {
        $this->gridId = $this->assertValidGridId($gridId);
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
     * @return bool
     */
    public function displaySharedFilters(): bool
    {
        return $this->displaySharedFilters;
    }

    /**
     * @return bool
     */
    public function displayTotals(): bool
    {
        return $this->displayTotals;
    }
}
