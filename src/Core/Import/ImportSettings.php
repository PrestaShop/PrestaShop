<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Import;

/**
 * Class ImportSettings provides import constants to be used in import pages.
 */
final class ImportSettings
{
    /**
     * Default value separator.
     */
    public const DEFAULT_SEPARATOR = ';';

    /**
     * Default multiple value separator.
     */
    public const DEFAULT_MULTIVALUE_SEPARATOR = ',';

    /**
     * Maximum number of columns that are visible in the import matches configuration page.
     */
    public const MAX_VISIBLE_COLUMNS = 6;

    /**
     * Maximum number of rows that are visible in the import matces configuration page.
     */
    public const MAX_VISIBLE_ROWS = 10;

    /**
     * This class cannot be instantiated.
     */
    private function __construct()
    {
    }
}
