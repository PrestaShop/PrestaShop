<?php

use PrestaShop\PrestaShop\Core\Foundation\Templating\RenderableProxy;
use Symfony\Component\Translation\TranslatorInterface;

abstract class AbstractFormCore implements FormInterface
{
    private $smarty;
    protected $translator;
    protected $constraintTranslator;

    protected $action;
    protected $template;

    protected $formatter;

    protected $formFields = [];
    protected $errors = ['' => []];

    public function __construct(
        Smarty $smarty,
        TranslatorInterface $translator,
        FormFormatterInterface $formatter
    ) {
        $this->smarty = $smarty;
        $this->translator = $translator;
        $this->formatter = $formatter;
        $this->constraintTranslator = new ValidateConstraintTranslator(
            $this->translator
        );
    }

    public function getFormatter()
    {
        return $this->formatter;
    }

    public function setAction($action)
    {
        $this->action = $action;
        return $this;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function getErrors()
    {
        foreach ($this->formFields as $field) {
            $this->errors[$field->getName()] = $field->getErrors();
        }
        return $this->errors;
    }

    public function hasErrors()
    {
        foreach ($this->getErrors() as $errors) {
            if (!empty($errors)) {
                return true;
            }
        }
        return false;
    }

    abstract public function getTemplateVariables();

    public function setTemplate($template)
    {
        $this->template = $template;
        return $this;
    }

    public function getTemplate()
    {
        return $this->template;
    }

    public function render(array $extraVariables = [])
    {
        $scope = $this->smarty->createData(
            $this->smarty
        );

        $scope->assign($extraVariables);
        $scope->assign($this->getTemplateVariables());

        $tpl = $this->smarty->createTemplate(
            $this->getTemplate(),
            $scope
        );

        return $tpl->fetch();
    }

    public function getProxy()
    {
        return new RenderableProxy($this);
    }

    public function validate()
    {
        foreach ($this->formFields as $field) {
            if ($field->isRequired() && !$field->getValue()) {
                $field->addError(
                    $this->constraintTranslator->translate('required')
                );
                continue;
            } elseif (!$field->isRequired() && !$field->getValue()) {
                continue;
            }

            foreach ($field->getConstraints() as $constraint) {
                if (!Validate::$constraint($field->getValue())) {
                    $field->addError(
                        $this->constraintTranslator->translate($constraint)
                    );
                }
            }
        }

        return !$this->hasErrors();
    }

    public function fillWith(array $params = [])
    {
        $newFields = $this->formatter->getFormat();

        foreach ($newFields as $field) {
            if (array_key_exists($field->getName(), $this->formFields)) {
                // keep current value if set
                $field->setValue($this->formFields[$field->getName()]->getValue());
            }

            if (array_key_exists($field->getName(), $params)) {
                // overwrite it if necessary
                $field->setValue($params[$field->getName()]);
            } elseif ($field->getType() === 'checkbox') {
                // checkboxes that are not submitted
                // are interpreted as booleans switched off
                $field->setValue(false);
            }
        }

        $this->formFields = $newFields;

        return $this;
    }

    public function getField($field_name)
    {
        if (array_key_exists($field_name, $this->formFields)) {
            return $this->formFields[$field_name];
        } else {
            return null;
        }
    }

    public function getValue($field_name)
    {
        $field = $this->getField($field_name);
        if ($field) {
            return $field->getValue();
        } else {
            return null;
        }
    }

    public function setValue($field_name, $value)
    {
        $this->getField($field_name)->setValue($value);
        return $this;
    }
}
