<?php

namespace PrestaShop\PrestaShop\Core\Table;

use Symfony\Component\Form\FormTypeInterface;

final class Column
{
    private $name;

    private $identifier;

    /**
     * @var callable
     */
    private $modifier;

    /**
     * @var FormTypeInterface
     */
    private $formType;

    public function __construct($identifier, $name, callable $modifier = null)
    {
        $this->name = $name;
        $this->identifier = $identifier;
        $this->modifier = $modifier;
    }

    public function setFormType($formType)
    {
        $this->formType = $formType;

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
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return callable
     */
    public function getModifier()
    {
        return $this->modifier;
    }

    /**
     * @return FormTypeInterface
     */
    public function getFormType()
    {
        return $this->formType;
    }
}