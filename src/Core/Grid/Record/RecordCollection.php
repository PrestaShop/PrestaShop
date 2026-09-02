<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Record;

use PrestaShop\PrestaShop\Core\Grid\Collection\AbstractCollection;

/**
 * Class RecordCollection is a wrapper around rows from database.
 */
final class RecordCollection extends AbstractCollection implements RecordCollectionInterface
{
    /**
     * @param array $records Raw records data
     */
    public function __construct(array $records = [])
    {
        $this->items = $records;
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return $this->items;
    }
}
