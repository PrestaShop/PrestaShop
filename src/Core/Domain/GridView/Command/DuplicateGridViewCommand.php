<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\GridView\Command;

use PrestaShop\PrestaShop\Core\Domain\GridView\Exception\GridViewConstraintException;
use PrestaShop\PrestaShop\Core\Domain\GridView\GridViewSettings;
use PrestaShop\PrestaShop\Core\Domain\GridView\ValueObject\GridViewId;

/**
 * Duplicates an accessible (own or shared) grid view as a private view of the current employee.
 */
class DuplicateGridViewCommand extends AbstractGridViewCommand
{
    private readonly GridViewId $gridViewId;

    private readonly string $copyName;

    /**
     * @param int $gridViewId
     * @param string $copyName
     *
     * @throws GridViewConstraintException
     */
    public function __construct(int $gridViewId, string $copyName)
    {
        $this->gridViewId = new GridViewId($gridViewId);
        $this->copyName = $this->assertValidName(mb_substr($copyName, 0, GridViewSettings::MAX_NAME_LENGTH));
    }

    /**
     * @return GridViewId
     */
    public function getGridViewId(): GridViewId
    {
        return $this->gridViewId;
    }

    /**
     * @return string
     */
    public function getCopyName(): string
    {
        return $this->copyName;
    }
}
