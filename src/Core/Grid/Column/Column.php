<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Grid\Column;

use Symfony\Component\Form\FormTypeInterface;

/**
 * Class Column is responsible for defining single column in row
 */
final class Column implements ColumnInterface
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var string Translated column name
     */
    private $name;

    /**
     * @var string  Unique column identifier
     */
    private $identifier;

    /**
     * @var callable|null Column's content modifier if needed
     */
    private $modifier;

    /**
     * @var string|null Fully qualified class name of Symfony's form type if column is filterable
     */
    private $filterFormType;

    /**
     * @var array Form type options for $filterFormType
     */
    private $filterFormTypeOptions = [];

    /**
     * @var bool True if column is sortable or False otherwise
     */
    private $isSortable = true;

    /**
     * @var int Column's position in grid
     */
    private $position = 0;

    /**
     * @param string $identifier Unique column identifier
     * @param string $name Translated column name
     * @param string $type
     */
    public function __construct($identifier, $name, $type = 'simple')
    {
        $this->name = $name;
        $this->identifier = $identifier;
        $this->type = $type;
    }

    /**
     * Create column from array data
     *
     * @param array $data
     *
     * @return Column
     */
    public static function fromArray(array $data)
    {
        $column = new self(
            $data['id'],
            $data['name'],
            isset($data['type']) ? $data['type'] : 'simple'
        );

        if (isset($data['position'])) {
            $column->setPosition($data['position']);
        }

        if (isset($data['modifier'])) {
            $column->setModifier($data['modifier']);
        }

        if (isset($data['filter_form_type'])) {
            $options = isset($data['filter_form_type_options']) ? $data['filter_form_type_options'] : [];

            $column->setFilterFormType($data['filter_form_type'], $options);
        }

        return $column;
    }

    /**
     * @param string $formType
     * @param array $options
     *
     * @return $this
     */
    public function setFilterFormType($formType, array $options = [])
    {
        if (!in_array(FormTypeInterface::class, class_implements($formType))) {
            throw new \InvalidArgumentException(sprintf(
                'Could not load type "%s": class does not implement %s',
                $formType,
                FormTypeInterface::class
            ));
        }

        $this->filterFormType = $formType;
        $this->filterFormTypeOptions = $options;

        return $this;
    }

    /**
     * @param bool $isSortable
     *
     * @return $this
     */
    public function setSortable($isSortable)
    {
        $this->isSortable = $isSortable;

        return $this;
    }

    /**
     * @param callable $modifier
     *
     * @return Column
     */
    public function setModifier(callable $modifier)
    {
        $this->modifier = $modifier;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->identifier;
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
    public function getModifier()
    {
        return $this->modifier;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilterFormType()
    {
        return $this->filterFormType;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilterFormTypeOptions()
    {
        return $this->filterFormTypeOptions;
    }

    /**
     * {@inheritdoc}
     */
    public function isSortable()
    {
        return $this->isSortable;
    }

    /**
     * {@inheritdoc}
     */
    public function isFilterable()
    {
        // if form type is set for column
        // then column is filterable
        return null !== $this->getFilterFormType();
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param int $position
     *
     * @return Column
     */
    public function setPosition($position)
    {
        $this->position = (int) $position;

        return $this;
    }

    /**
     * Get column type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}
