<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Import\EntityField;

use ArrayAccess;
use Countable;
use IteratorAggregate;

/**
 * Interface EntityFieldCollectionInterface describes a collection of entity fields.
 */
interface EntityFieldCollectionInterface extends ArrayAccess, Countable, IteratorAggregate
{
    /**
     * Add an entity field to the collection.
     *
     * @param EntityFieldInterface $entityField
     *
     * @return self
     */
    public function addEntityField(EntityFieldInterface $entityField);

    /**
     * Get required fields from the collection.
     *
     * @return array
     */
    public function getRequiredFields();

    /**
     * Creates a collection from array of entity fields.
     *
     * @param array $entityFields array of objects implementing EntityFieldInterface
     *
     * @return self
     */
    public static function createFromArray(array $entityFields);

    /**
     * Converts the collection to array.
     *
     * @return array
     */
    public function toArray();
}
