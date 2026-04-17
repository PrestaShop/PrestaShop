<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Import\File\DataCell;

/**
 * Class EmptyDataCell defines an empty data cell.
 */
final class EmptyDataCell implements DataCellInterface
{
    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getKey()
    {
        return '';
    }
}
