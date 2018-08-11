<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
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

    public function __construct(Currency $currency, PriceFormatter $priceFormatter)
    {
        $this->currency = $currency;
        $this->priceFormatter  = $priceFormatter;
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
        $attributes['picos'] = $this->addPicos($attributes);
        $attributes['price'] = $this->getModulePrice($attributes['price']);
        $attributes['starsRate'] = str_replace('.', '', round($attributes['avgRate'] * 2) / 2); // Round to the nearest 0.5
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

    /**
     * Generate the list of small icons to be displayed near the module name
     * 
     * @param array $attributes Attributes of presented module
     * @return array
     */
    private function addPicos(array $attributes)
    {
        $picos = array();

        // PrestaTrust display
        if (!empty($attributes['prestatrust']) && !empty($attributes['prestatrust']->pico)) {
            $text = '';
            $class = '';
            if (isset($attributes['prestatrust']->status)) {
                $text = $attributes['prestatrust']->status ? 'OK' : 'KO';
                $class = $attributes['prestatrust']->status ? 'text-success' : 'text-warning';
            }
            $picos['prestatrust'] = array(
                'img' => $attributes['prestatrust']->pico,
                'label' => 'prestatrust',
                'text' => $text,
                'class' => $class,
            );
        }
        return $picos;
    }
}
