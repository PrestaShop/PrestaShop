<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Action\Bulk;

/**
 * Interface BulkActionInterface defines contract for single grid bulk action.
 */
interface BulkActionInterface
{
    /**
     * Get unique bulk action identifier for grid.
     *
     * @return string
     */
    public function getId();

    /**
     * Get translated bulk action name.
     *
     * @return string
     */
    public function getName();

    /**
     * Get action type.
     *
     * @return string
     */
    public function getType();

    /**
     * Get action options.
     *
     * @return array
     */
    public function getOptions();

    /**
     * Set options for bulk action.
     *
     * @param array $options
     *
     * @return self
     */
    public function setOptions(array $options);

    /**
     * Set bulk action name.
     *
     * @param string $name
     *
     * @return self
     */
    public function setName(string $name);

    /**
     * Returns action icon name.
     *
     * @return string
     */
    public function getIcon();

    /**
     * Set action icon name.
     *
     * @param string $icon
     *
     * @return self
     */
    public function setIcon(string $icon);
}
