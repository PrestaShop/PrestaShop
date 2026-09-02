<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Presenter\Module;

use Currency;
use Exception;
use Hook;
use Module as LegacyModule;
use PrestaShop\PrestaShop\Adapter\Presenter\PresenterInterface;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Core\Module\ModuleCollection;
use PrestaShop\PrestaShop\Core\Module\ModuleInterface;

class ModulePresenter implements PresenterInterface
{
    /**
     * @var Currency
     */
    private $currency;

    /** @var PriceFormatter */
    private $priceFormatter;

    public function __construct(Currency $currency, PriceFormatter $priceFormatter)
    {
        $this->currency = $currency;
        $this->priceFormatter = $priceFormatter;
    }

    /**
     * @param ModuleInterface $module
     *
     * @return array
     */
    public function present($module)
    {
        if (!($module instanceof ModuleInterface)) {
            throw new Exception('ModulePresenter can only present instance of Module');
        }

        $attributes = $module->attributes->all();
        $attributes['id'] = $module->database->get('id', $attributes['id']);
        $attributes['price'] = $this->getModulePrice($attributes['price']);
        // Round to the nearest 0.5
        $attributes['starsRate'] = str_replace('.', '', (string) (round(floatval($attributes['avgRate']) * 2) / 2));

        $moduleInstance = $module->getInstance();

        if ($moduleInstance instanceof LegacyModule) {
            $attributes['multistoreCompatibility'] = $moduleInstance->getMultistoreCompatibility();
        }

        $result = [
            'attributes' => $attributes,
            'disk' => $module->disk->all(),
            'database' => $module->database->all(),
        ];

        Hook::exec('actionPresentModule',
            ['presentedModule' => &$result]
        );

        return $result;
    }

    private function getModulePrice($prices)
    {
        $iso_code = $this->currency->iso_code;
        if (array_key_exists($iso_code, $prices)) {
            $prices['displayPrice'] = $this->priceFormatter->convertAndFormat($prices[$iso_code]);
            $prices['raw'] = $prices[$iso_code];
        } else {
            $prices['displayPrice'] = '$' . $prices['USD'];
            $prices['raw'] = $prices['USD'];
        }

        return $prices;
    }

    /**
     * Transform a collection of addons as a simple array of data.
     *
     * @param ModuleCollection|array $modules
     *
     * @return array
     */
    public function presentCollection($modules)
    {
        $presentedModules = [];

        foreach ($modules as $name => $module) {
            $presentedModules[$name] = $this->present($module);
        }

        return $presentedModules;
    }
}
