<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Action\Row;

/**
 * Interface RowActionInterface defines contract for grid's row action.
 */
interface RowActionInterface
{
    /**
     * Get unique row id for grid row's action.
     *
     * @return string
     */
    public function getId();

    /**
     * Get action type.
     *
     * @return string
     */
    public function getType();

    /**
     * Get translated row action name.
     *
     * @return string
     */
    public function getName();

    /**
     * Set action name.
     *
     * @param string $name
     *
     * @return self
     */
    public function setName($name);

    /**
     * Get row action icon.
     *
     * @return string
     */
    public function getIcon();

    /**
     * Set action icon.
     *
     * @param string $icon
     *
     * @return self
     */
    public function setIcon($icon);

    /**
     * Get action options.
     *
     * @return array
     */
    public function getOptions();

    /**
     * Set action options.
     *
     * @param array $options
     *
     * @return self
     */
    public function setOptions(array $options);

    /**
     * Check if action is applicable for given record.
     *
     * @param array $record
     *
     * @return bool
     */
    public function isApplicable(array $record);
}
