<?php

use PrestaShop\PrestaShop\Core\Business\Product\ProductPresenter;
use PrestaShop\PrestaShop\Core\Business\Product\ProductPresentationSettings;

abstract class ProductPresentingFrontControllerCore extends FrontController
{
    private function getFactory()
    {
        return new ProductPresenterFactory($this->context);
    }

    protected function getProductPresentationSettings()
    {
        return $this->getFactory()->getPresentationSettings();
    }

    protected function getProductPresenter()
    {
        return $this->getFactory()->getPresenter();
    }
}
