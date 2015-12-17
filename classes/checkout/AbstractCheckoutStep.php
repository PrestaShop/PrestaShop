<?php

use Symfony\Component\Translation\TranslatorInterface;

abstract class AbstractCheckoutStepCore implements CheckoutStepInterface
{
    private $smarty;
    private $translator;
    private $checkoutProcess;

    private $title;

    public function __construct(Smarty $smarty, TranslatorInterface $translator)
    {
        $this->smarty = $smarty;
        $this->translator = $translator;
    }

    protected function getTranslator()
    {
        return $this->translator;
    }

    public function init(array $requestParameters = [])
    {
    }

    protected function renderTemplate($template, array $params = [])
    {
        $defaultParams = [
            'title' => $this->getTitle()
        ];

        $scope = $this->smarty->createData(
            $this->smarty
        );

        $scope->assign(array_merge($defaultParams, $params));

        $tpl = $this->smarty->createTemplate(
            $template,
            $scope
        );

        return $tpl->fetch();
    }

    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setCheckoutProcess(CheckoutProcess $checkoutProcess)
    {
        $this->checkoutProcess = $checkoutProcess;
        return $this;
    }

    public function getCheckoutProcess()
    {
        return $this->checkoutProcess;
    }
}
