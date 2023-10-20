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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShopBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * This class is used by Twig_Environment and provide some methods callable from a twig template.
 */
class DataFormatterExtension extends AbstractExtension
{
    /**
     * Define available filters.
     *
     * @return array Twig_SimpleFilter
     */
    public function getFilters()
    {
        return [
            new TwigFilter('arrayCast', [$this, 'arrayCast']),
            new TwigFilter('intCast', [$this, 'intCast']),
            new TwigFilter('unsetElement', [$this, 'unsetElement']),
            new TwigFilter('array_pluck', [$this, 'arrayPluck']),
        ];
    }

    /**
     * Define available functions.
     *
     * @return array Twig_SimpleFunction
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('arrayCast', [$this, 'arrayCast']),
            new TwigFunction('intCast', [$this, 'intCast']),
            new TwigFunction('unsetElement', [$this, 'unsetElement']),
            new TwigFunction('array_pluck', [$this, 'arrayPluck']),
        ];
    }

    /**
     * Cast to array the variable given.
     *
     * @param mixed $toCast Mixed value to be casted into an array
     *
     * @return array $toCast casted in array
     */
    public function arrayCast($toCast)
    {
        return (array) $toCast;
    }

    /**
     * Cast to int the variable given.
     *
     * @param mixed $toCast Mixed value to be casted into an int
     *
     * @return int $toCast casted in int
     */
    public function intCast($toCast)
    {
        return (int) $toCast;
    }

    /**
     * PHP 'unset()' exposed through twig template engine.
     *
     * @param array $array Array containing Element to unset
     * @param string $key Element to be unset
     */
    public function unsetElement($array, $key)
    {
        unset($array[$key]);

        return $array;
    }

    /**
     * Extract a subset of an array and returns only the wanted keys.
     * If $extractedKeys is an associative array you can even rename the
     * keys of the extracted array.
     *
     * ex:
     *  arrayPluck(['first_name' => 'John', 'last_name' => 'Doe'], ['first_name']) => ['first_name' => 'John']
     *  arrayPluck(['first_name' => 'John', 'last_name' => 'Doe'], ['first_name' => 'name']) => ['name' => 'John']
     *
     * @param array $array
     * @param array $extractedKeys
     *
     * @return array
     */
    public function arrayPluck(array $array, array $extractedKeys)
    {
        $extractedArray = [];
        foreach ($extractedKeys as $key => $value) {
            if (is_int($key)) {
                $oldKey = $value;
                $newKey = $value;
            } else {
                $oldKey = $key;
                $newKey = $value;
            }
            if (isset($array[$oldKey])) {
                $extractedArray[$newKey] = $array[$oldKey];
            }
        }

        return $extractedArray;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'twig_data_formatter_extension';
    }
}
