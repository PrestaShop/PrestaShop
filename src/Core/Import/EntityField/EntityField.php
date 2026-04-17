<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Import\EntityField;

/**
 * Class EntityField defines an entity field.
 */
final class EntityField implements EntityFieldInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $label;

    /**
     * @var string
     */
    private $description;

    /**
     * @var bool
     */
    private $required;

    /**
     * @param string $name
     * @param string $label
     * @param string $description
     * @param bool $required
     */
    public function __construct($name, $label, $description = '', $required = false)
    {
        $this->name = $name;
        $this->label = $label;
        $this->description = $description;
        $this->required = $required;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get field's label.
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Get field's description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * {@inheritdoc}
     */
    public function isRequired()
    {
        return $this->required;
    }
}
