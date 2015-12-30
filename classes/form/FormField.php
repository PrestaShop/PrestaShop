<?php

class FormFieldCore
{
    private $name               = '';
    private $type               = 'text';
    private $required           = false;
    private $label              = '';
    private $value              = null;
    private $availableValues    = [];
    private $errors             = [];

    public function toArray()
    {
        return [
            'name' => $this->getName(),
            'type' => $this->getType(),
            'required' => $this->isRequired(),
            'label' => $this->getLabel(),
            'value' => $this->getValue(),
            'availableValues' => $this->getAvailableValues(),
            'errors' => $this->getErrors()
        ];
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setRequired($required)
    {
        $this->required = $required;
        return $this;
    }

    public function isRequired()
    {
        return $this->required;
    }

    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setAvailableValues(array $availableValues)
    {
        $this->availableValues = $availableValues;
        return $this;
    }

    public function getAvailableValues()
    {
        return $this->availableValues;
    }

    public function addAvailableValue($availableValue, $label = null)
    {
        if (!$label) {
            $label = $availableValue;
        }

        $this->availableValues[$availableValue] = $label;
        return $this;
    }

    public function setErrors(array $errors)
    {
        $this->errors = $errors;
        return $this;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function addError($errorString)
    {
        $this->errors[] = $errorString;
        return $this;
    }
}
