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
     */
    public function isRequired();
}
