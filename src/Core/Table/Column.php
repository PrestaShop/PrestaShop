<?php

namespace PrestaShop\PrestaShop\Core\Table;

use Symfony\Component\Form\FormTypeInterface;

/**
 * Class Column is responsible for defining single column in row
 */
final class Column
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $identifier;

    /**
     * @var callable|null
     */
    private $modifier;

    /**
     * @var string
     */
    private $formType;

    /**
     * @var bool
     */
    private $isSortable = true;

    /**
     * @var array|null
     */
    private $formTypeOptions;

    /**
     * @param string $identifier        Unique column identifier
     * @param string $name              Translated column name
     * @param callable|null $modifier   Callable to modify column's content if needed
     */
    public function __construct($identifier, $name, callable $modifier = null)
    {
        $this->name = $name;
        $this->identifier = $identifier;
        $this->modifier = $modifier;
    }

    /**
     * @param string $formType
     * @param array $options
     *
     * @return $this
     */
    public function setFormType($formType, array $options = [])
    {
        if (!in_array(FormTypeInterface::class, class_implements($formType))) {
            throw new \InvalidArgumentException(sprintf(
                'Could not load type "%s": class does not implement %s',
                $formType,
                FormTypeInterface::class
            ));
        }

        $this->formType = $formType;
        $this->formTypeOptions = $options;

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
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return callable|null
     */
    public function getModifier()
    {
        return $this->modifier;
    }

    /**
     * @return string
     */
    public function getFormType()
    {
        return $this->formType;
    }

    /**
     * @return array|null
     */
    public function getFormTypeOptions()
    {
        return $this->formTypeOptions;
    }

    /**
     * @return bool
     */
    public function isSortable()
    {
        return $this->isSortable;
    }
}
