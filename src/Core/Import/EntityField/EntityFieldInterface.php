<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Import\EntityField;

/**
 * Interface EntityFieldInterface describes an entity field.
 */
interface EntityFieldInterface
{
    /**
     * Get field's name.
     *
     * @return string
     */
    public function getName();

    /**
     * Get field's label.
     *
     * @return string
     */
    public function getLabel();

    /**
     * Get field's description.
     *
     * @return string
     */
    public function getDescription();

    /**
     * Checks if the field is required.
     *
     * @return bool
     *
     * @deprecated since 9.3, will be removed in the next major version - the flag
     *             only ever drove mapping-screen hints (a "*" on the column label and a
     *             client-side check), never a server-side rule. The import engine
     *             validates required values PER ROW instead, because the same file can
     *             create and update entities and a value is only mandatory when the row
     *             creates one - see the row validator of each importer, e.g.
     *             \PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Product\ProductRowValidator
     */
    public function isRequired();
}
