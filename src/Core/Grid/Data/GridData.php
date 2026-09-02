<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Data;

use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollectionInterface;

/**
 * Class GridData is responsible for storing grid data.
 */
final class GridData implements GridDataInterface
{
    /**
     * @var RecordCollectionInterface
     */
    private $records;

    /**
     * @var int
     */
    private $recordsTotal;

    /**
     * @var string
     */
    private $query;

    /**
     * @param RecordCollectionInterface $records Filtered & paginated rows data
     * @param int $recordsTotal Total number of rows (without pagination)
     * @param string $query Query used to get rows
     */
    public function __construct(RecordCollectionInterface $records, $recordsTotal, $query = '')
    {
        $this->records = $records;
        $this->recordsTotal = $recordsTotal;
        $this->query = $query;
    }

    /**
     * {@inheritdoc}
     */
    public function getRecords()
    {
        return $this->records;
    }

    /**
     * {@inheritdoc}
     */
    public function getRecordsTotal()
    {
        return $this->recordsTotal;
    }

    /**
     * {@inheritdoc}
     */
    public function getQuery()
    {
        return $this->query;
    }
}
