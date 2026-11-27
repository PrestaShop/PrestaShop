<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\GridView\Command;

use PrestaShop\PrestaShop\Core\Domain\GridView\Exception\GridViewConstraintException;
use PrestaShop\PrestaShop\Core\Domain\GridView\GridViewSettings;

abstract class AbstractGridViewCommand
{
    /**
     * @param string $name
     *
     * @return string
     *
     * @throws GridViewConstraintException
     */
    protected function assertValidName(string $name): string
    {
        if ('' === trim($name) || mb_strlen($name) > GridViewSettings::MAX_NAME_LENGTH) {
            throw new GridViewConstraintException(
                sprintf('Invalid grid view name "%s"', $name),
                GridViewConstraintException::INVALID_NAME
            );
        }

        return $name;
    }

    /**
     * @param string $gridId
     *
     * @return string
     *
     * @throws GridViewConstraintException
     */
    protected function assertValidGridId(string $gridId): string
    {
        if (!preg_match(GridViewSettings::GRID_ID_PATTERN, $gridId)) {
            throw new GridViewConstraintException(
                sprintf('Invalid grid id "%s"', $gridId),
                GridViewConstraintException::INVALID_GRID_ID
            );
        }

        return $gridId;
    }

    /**
     * The filter id must belong to the grid: it either equals the grid id or is
     * prefixed by it (dynamic filter ids like "product_combinations_{id}").
     *
     * @param string $gridId
     * @param string $filterId
     *
     * @return string
     *
     * @throws GridViewConstraintException
     */
    protected function assertFilterIdBelongsToGrid(string $gridId, string $filterId): string
    {
        if ($filterId !== $gridId && !str_starts_with($filterId, $gridId . '_')) {
            throw new GridViewConstraintException(
                sprintf('Filter id "%s" does not belong to grid "%s"', $filterId, $gridId),
                GridViewConstraintException::INVALID_FILTER_ID
            );
        }

        return $filterId;
    }
}
