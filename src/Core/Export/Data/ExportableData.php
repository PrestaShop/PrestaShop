<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Export\Data;

/**
 * Class ExportableData stores data that should be written to export file.
 */
final class ExportableData implements ExportableDataInterface
{
    /**
     * @var string[]
     */
    private $titles;

    /**
     * @var array
     */
    private $rows;

    /**
     * @param string[] $titles
     * @param array $rows
     */
    public function __construct(array $titles, array $rows)
    {
        $this->titles = $titles;
        $this->rows = $rows;
    }

    /**
     * {@inheritdoc}
     */
    public function getTitles()
    {
        return $this->titles;
    }

    /**
     * {@inheritdoc}
     */
    public function getRows()
    {
        return $this->rows;
    }
}
