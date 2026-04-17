<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Export\Data;

/**
 * Interface ExportableDataInterface.
 */
interface ExportableDataInterface
{
    /**
     * Titles data.
     *
     * @return string[]
     */
    public function getTitles();

    /**
     * Rows data.
     *
     * @return array[]
     */
    public function getRows();
}
