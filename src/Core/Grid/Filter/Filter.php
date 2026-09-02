<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Filter;

/**
 * Class Filter defines single filter for grid.
 */
final class Filter implements FilterInterface
{
    /**
     * @var string Fully qualified filter type class name
     */
    private $type;

    /**
     * @var array Filter type options
     */
    private $typeOptions = [];

    /**
     * @var string Filter name
     */
    private $name;

    /**
     * @var string|null Column ID if filter is associated with columns
     */
    private $column;

    /**
     * @param string $name
     * @param string $filterFormType
     */
    public function __construct($name, $filterFormType)
    {
        $this->type = $filterFormType;
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function setTypeOptions(array $filterTypeOptions)
    {
        $this->typeOptions = $filterTypeOptions;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeOptions()
    {
        return $this->typeOptions;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setAssociatedColumn($columnId)
    {
        $this->column = $columnId;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAssociatedColumn()
    {
        return $this->column;
    }
}
