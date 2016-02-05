<?php

interface FormInterface
{
    public function setAction($action);
    public function fillWith(array $params = []);
    public function handleRequest(array $params = []);
    public function wasSubmitted();
    public function submit();
    public function getErrors();
    public function hasErrors();
    public function render();
    public function setTemplate($template);
}
