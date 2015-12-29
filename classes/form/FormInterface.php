<?php

interface FormInterface
{
    public function setAction($action);
    public function fillWith(array $params = []);
    public function submit();
    public function getErrors();
    public function hasErrors();
    public function render(array $extraVariables = []);
    public function setTemplate($template);
}
