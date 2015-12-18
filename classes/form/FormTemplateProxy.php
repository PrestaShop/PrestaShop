<?php

class FormTemplateProxyCore
{
    private $form;

    public function __construct(FormInterface $form)
    {
        $this->form = $form;
    }

    public function setTemplate($template)
    {
        $this->form->setTemplate($template);
        return $this;
    }

    public function render()
    {
        return $this->form->render();
    }
}
