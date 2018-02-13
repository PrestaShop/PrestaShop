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

namespace PrestaShopBundle\Twig;

/**
 * This class is used by Twig_Environment and provide some methods callable from a twig template
 */
class DataFormatterExtension extends \Twig_Extension
{
    /**
     * Define available filters
     *
     * @return array Twig_SimpleFilter
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('arrayCast', array($this, 'arrayCast')),
            new \Twig_SimpleFilter('intCast', array($this, 'intCast')),
            new \Twig_SimpleFilter('unsetElement', array($this, 'unsetElement')),
        );
    }

    /**
     * Define available functions
     *
     * @return array Twig_SimpleFunction
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('arrayCast', array($this, 'arrayCast')),
            new \Twig_SimpleFunction('intCast', array($this, 'intCast')),
            new \Twig_SimpleFunction('unsetElement', array($this, 'unsetElement')),
        );
    }

    /**
     * Cast to array the variable given
     *
     * @param mixed $toCast Mixed value to be casted into an array
     *
     * @return array $toCast casted in array
     */
    public function arrayCast($toCast)
    {
        return (array)$toCast;
    }

    /**
     * Cast to int the variable given
     *
     * @param mixed $toCast Mixed value to be casted into an int
     *
     * @return int $toCast casted in int
     */
    public function intCast($toCast)
    {
        return (int)$toCast;
    }

    /**
     * PHP 'unset()' exposed through twig template engine
     *
     * @param string $array Array containing Element to unset
     * @param string $key Element to be unset
     *
     */
    public function unsetElement($array, $key)
    {
        unset($array[$key]);
        return $array;
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
