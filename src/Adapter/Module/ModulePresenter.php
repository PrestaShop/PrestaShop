<?php

namespace PrestaShop\PrestaShop\Adapter\Module;

use Currency;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Core\Foundation\Templating\PresenterInterface;

class ModulePresenter implements PresenterInterface
{
    /**
     * @var Currency
     */
    private $currency;

    /** @var PriceFormatter */
    private $priceFormatter;

    public function __construct(Currency $currency)
    {
        $this->currency = $currency;
        // Not declared as a Symfony service :(
        $this->priceFormatter  = new PriceFormatter();
    }

    /**
     * @param Module $module
     *
     * @return array
     */
    public function present($module)
    {
        if (!is_a($module, '\\PrestaShop\\PrestaShop\\Adapter\\Module\\Module')) {
            throw new \Exception("ModulePresenter can only present instance of Module");
        }

        $attributes = $module->attributes->all();
        $attributes['price'] = $this->getModulePrice($attributes['price']);

        return array(
            'attributes' => $attributes,
            'disk' => $module->disk->all(),
            'database' => $module->database->all(),
        );
    }

    private function getModulePrice($prices)
    {
        $iso_code = $this->currency->iso_code;
        if (array_key_exists($iso_code, $prices)) {
            $prices['displayPrice'] = $this->priceFormatter->convertAndFormat($prices[$iso_code]);
            $prices['raw'] = $prices[$iso_code];
        } else {
            $prices['displayPrice'] = '$'.$prices['USD'];
            $prices['raw'] = $prices['USD'];
        }
        return $prices;
    }
}
