<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Filter;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;

/**
 * This allows to store a filter parameter in a hidden input which can be prefilled
 * to provide some context or updated dynamically via js for filter component outside
 * the grid headers filters.
 */
class HiddenFilter implements FilterInterface
{
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
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return HiddenType::class;
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
