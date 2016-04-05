<?php

namespace PrestaShop\PrestaShop\Adapter\Module;

use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Core\Foundation\Templating\PresenterInterface;

class ModulePresenter implements PresenterInterface
{
    /* @var PriceFormatter */
    private $priceFormatter;

    public function __construct()
    {
        $this->priceFormatter  = new PriceFormatter();
    }

    /**
     * @param Module $module
     *
     * @return array
     */
    public function present($module)
    {
        /*if (!is_a($module, '\\PrestaShop\\PrestaShop\\Adapter\\Module')) {
            throw new \Exception("ModulePresenter can only present instance of Module");
        }*/

        $attributes = $module->attributes->all();
        $attributes['price'] = $this->priceFormatter->convertAndFormat($attributes['price']['USD']);

        return array(
            'attributes' => $attributes,
            'disk' => $module->disk->all(),
            'database' => $module->database->all(),
        );
    }
}
