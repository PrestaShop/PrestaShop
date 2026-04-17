<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Column;

/**
 * Interface ColumnInterface exposes contract for single column.
 */
interface ColumnInterface
{
    /**
     * Get unique column id.
     *
     * @return string
     */
    public function getId();

    /**
     * Get column type.
     *
     * @return string
     */
    public function getType();

    /**
     * Get translated column name.
     *
     * @return string
     */
    public function getName();

    /**
     * Translated column name.
     *
     * @param string $name
     *
     * @return self
     */
    public function setName($name);

    /**
     * Get column related options.
     *
     * @return array
     */
    public function getOptions();

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function getOption(string $name);

    /**
     * Set column options.
     *
     * @param array $options
     *
     * @return self
     */
    public function setOptions(array $options);
}
