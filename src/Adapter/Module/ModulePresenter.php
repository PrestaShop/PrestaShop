<?php

namespace PrestaShop\PrestaShop\Adapter\Module;

use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Core\Foundation\Templating\PresenterInterface;

class ModulePresenter implements PresenterInterface
{
    /**
     * @var LegacyContext 
     */
    private $context;

    /** @var PriceFormatter */
    private $priceFormatter;

    public function __construct(LegacyContext $context)
    {
        $this->context = $context;
        
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
        $currency = $this->context->getContext()->currency;
        if (array_key_exists($currency->iso_code, $prices)) {
            $prices['displayPrice'] = $this->priceFormatter->convertAndFormat($prices[$currency->iso_code]);
            $prices['raw'] = $prices[$currency->iso_code];
        } else {
            $prices['displayPrice'] = '$'.$prices['USD'];
            $prices['raw'] = $prices['USD'];
        }
        return $prices;
    }
}
