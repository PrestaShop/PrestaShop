<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
