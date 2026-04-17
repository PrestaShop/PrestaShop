<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Action;

/**
 * Interface GridActionInterface.
 */
interface GridActionInterface
{
    /**
     * Return unique action identifier.
     *
     * @return string
     */
    public function getId();

    /**
     * Returns translated action name.
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
    public function setIcon($icon);

    /**
     * Returns grid action type.
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
     * Set action options.
     *
     * @param array $options
     *
     * @return self
     */
    public function setOptions(array $options);
}
