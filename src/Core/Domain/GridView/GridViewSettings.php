<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\GridView;

/**
 * Domain-wide grid view settings
 */
final class GridViewSettings
{
    public const MAX_VIEWS_PER_CONFIGURATION = 30;

    public const MAX_NAME_LENGTH = 255;

    public const GRID_ID_PATTERN = '/^[a-zA-Z0-9_-]+$/';
}
