<?php

use PrestaShop\PrestaShop\Core\Foundation\Templating\RenderableProxy;

abstract class AbstractFormCore implements FormInterface
{
    private $smarty;
    protected $action;
    protected $errors = [];
    protected $template;

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
}
