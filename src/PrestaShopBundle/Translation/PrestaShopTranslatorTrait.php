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

namespace PrestaShopBundle\Translation;

trait PrestaShopTranslatorTrait
{
    public static $regexSprintfParams = '#(?:%%|%(?:[0-9]+\$)?[+-]?(?:[ 0]|\'.)?-?[0-9]*(?:\.[0-9]+)?[bcdeufFosxX])#';
    public static $regexClassicParams = '/%\w+%/';

    /**
     * {@inheritdoc}
     */
    public function trans($id, array $parameters = array(), $domain = null, $locale = null)
    {
        if (null !== $domain) {
            $domain = str_replace('.', '', $domain);
        }

        if (isset($parameters['legacy'])) {
            $legacy = $parameters['legacy'];
            unset($parameters['legacy']);
        }

        $translated = parent::trans($id, array(), $domain, $locale);
        if (isset($legacy) && 'htmlspecialchars' === $legacy) {
            $translated = call_user_func($legacy, $translated, ENT_NOQUOTES);
        } elseif (isset($legacy)) {
            $translated = call_user_func($legacy, $translated);
        }

        if (!empty($parameters) && $this->isSprintfString($id)) {
            $translated = vsprintf($translated, $parameters);
        } elseif (!empty($parameters)) {
            $translated = strtr($translated, $parameters);
        }

        return $translated;
    }

    public function getSourceString($translated, $domain, $locale = null)
    {
        if (empty($domain)) {
            return $translated;
        }

        $domain = str_replace('.', '', $domain);
        $contextCatalog = $this->getCatalogue()->all($domain);

        if ($untranslated = array_search($translated, $contextCatalog)) {
            return $untranslated;
        }

        return $translated;
    }

    /**
     * {@inheritdoc}
     */
    public function transChoice($id, $number, array $parameters = array(), $domain = null, $locale = null)
    {
        if (null !== $domain) {
            $domain = str_replace('.', '', $domain);
        }

        if (!$this->isSprintfString($id)) {
            return parent::transChoice($id, $number, $parameters, $domain, $locale);
        }

        return vsprintf(parent::transChoice($id, $number, array(), $domain, $locale), $parameters);
    }

    /**
     * @param string $string
     *
     * @return bool
     */
    final private function isSprintfString($string)
    {
        return (bool) preg_match_all(static::$regexSprintfParams, $string)
            && !(bool) preg_match_all(static::$regexClassicParams, $string);
    }
}
