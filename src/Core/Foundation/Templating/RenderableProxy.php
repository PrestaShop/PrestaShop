<?php

namespace PrestaShop\PrestaShop\Core\Foundation\Templating;

class RenderableProxy
{
    private $renderable;

    public function __construct(RenderableInterface $renderable)
    {
        $this->renderable = $renderable;
    }

    public function setTemplate($templatePath)
    {
        $this->renderable->setTemplate($templatePath);
        return $this;
    }

    public function getTemplate()
    {
        return $this->renderable->getTemplate();
    }

    public function render(array $extraParams = [])
    {
        return $this->renderable->render($extraParams);
    }
}
