<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Data;

use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollectionInterface;

/**
 * Interface GridDataInterface exposes contract for final grid data.
 */
interface GridDataInterface
{
    /**
     * Returns final grid rows ready for rendering.
     *
     * @return RecordCollectionInterface
     */
    public function getRecords();

    /**
     * Returns total rows in data source.
     *
     * @return int
     */
    public function getRecordsTotal();

    /**
     * Return query which was used to get rows.
     *
     * @return string
     */
    public function getQuery();
}
