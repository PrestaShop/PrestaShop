<?php

namespace PrestaShop\PrestaShop\Core\Foundation\Templating;

interface RenderableInterface
{
    public function setTemplate($templatePath);
    public function getTemplate();
    public function render(array $extraParams = []);
}
