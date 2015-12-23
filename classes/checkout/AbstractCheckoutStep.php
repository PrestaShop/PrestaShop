<?php

use Symfony\Component\Translation\TranslatorInterface;

abstract class AbstractCheckoutStepCore implements CheckoutStepInterface
{
    private $smarty;
    private $translator;
    private $checkoutProcess;

    private $title;

    protected $step_is_reachable = false;
    protected $step_is_complete  = false;
    protected $step_is_current   = false;

    public function __construct(Smarty $smarty, TranslatorInterface $translator)
    {
        $this->smarty = $smarty;
        $this->translator = $translator;
    }

    protected function getTranslator()
    {
        return $this->translator;
    }

    protected function renderTemplate($template, array $extraParams = [], array $params = [])
    {
        $defaultParams = [
            'title' => $this->getTitle(),
            'step_is_complete' => (int)$this->isComplete(),
            'step_is_reachable' => (int)$this->isReachable(),
            'step_is_current' => (int)$this->isCurrent(),
        ];

        $scope = $this->smarty->createData(
            $this->smarty
        );

        $scope->assign(array_merge($defaultParams, $extraParams, $params));

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

    public function getCheckoutSession()
    {
        return $this->getCheckoutProcess()->getCheckoutSession();
    }

    public function setReachable($step_is_reachable)
    {
        $this->step_is_reachable = $step_is_reachable;
        return $this;
    }

    public function isReachable()
    {
        return $this->step_is_reachable;
    }

    public function setComplete($step_is_complete)
    {
        $this->step_is_complete = $step_is_complete;
        return $this;
    }

    public function isComplete()
    {
        return $this->step_is_complete;
        ;
    }

    public function setCurrent($step_is_current)
    {
        $this->step_is_current = $step_is_current;
        return $this;
    }

    public function isCurrent()
    {
        return $this->step_is_current;
    }

    public function getIdentifier()
    {
        return get_class($this);
    }

    public function getDataToPersist()
    {
        return [];
    }

    public function restorePersistedData(array $data)
    {
        return $this;
    }
}
