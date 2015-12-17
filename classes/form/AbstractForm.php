<?php

abstract class AbstractFormCore implements FormInterface
{
    private $smarty;
    protected $action;
    protected $errors = [];

    public function __construct(Smarty $smarty)
    {
        $this->smarty = $smarty;
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
        return $this->errors;
    }

    public function hasErrors()
    {
        foreach ($this->errors as $errors) {
            if (!empty($errors)) {
                return true;
            }
        }
        return false;
    }

    abstract public function getTemplateVariables();
    abstract public function getTemplatePath();

    public function render()
    {
        $scope = $this->smarty->createData(
            $this->smarty
        );

        $scope->assign($this->getTemplateVariables());

        $tpl = $this->smarty->createTemplate(
            $this->getTemplatePath(),
            $scope
        );

        return $tpl->fetch();
    }
}
