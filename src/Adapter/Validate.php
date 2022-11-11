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

namespace PrestaShop\PrestaShop\Adapter;

use Validate as ValidateLegacy;

/**
 * Adapter for Validate Legacy class.
 */
class Validate
{
    /**
     * @param mixed $way
     *
     * @return bool
     */
    public static function isOrderWay($way)
    {
        return ValidateLegacy::isOrderWay($way);
    }

    /**
     * @param mixed $order
     *
     * @return bool
     */
    public static function isOrderBy($order)
    {
        return ValidateLegacy::isOrderBy($order);
    }

    /**
     * @param mixed $date
     *
     * @return bool
     */
    public static function isDate($date)
    {
        return ValidateLegacy::isDate($date);
    }

    /**
     * Check if HTML content is clean.
     *
     * @param string $html
     * @param bool $allowIframe
     *
     * @return bool
     */
    public function isCleanHtml($html, $allowIframe = false)
    {
        return ValidateLegacy::isCleanHtml($html, $allowIframe);
    }

    /**
     * Check for a given email validity.
     *
     * @param string $email
     *
     * @return bool
     */
    public function isEmail(string $email): bool
    {
        return ValidateLegacy::isEmail($email);
    }

    /**
     * Check for module name validity.
     *
     * @param string $name Module name to validate
     *
     * @return bool
     */
    public function isModuleName($name)
    {
        return ValidateLegacy::isModuleName($name);
    }

    /**
     * Check if object has been correctly loaded.
     *
     * @param object $object Object to validate
     *
     * @return bool Validity is ok or not
     */
    public static function isLoadedObject($object)
    {
        return ValidateLegacy::isLoadedObject($object);
    }

    /**
     * Check for Language Iso Code.
     *
     * @param string $isoCode
     *
     * @return bool
     */
    public function isLangIsoCode($isoCode)
    {
        return ValidateLegacy::isLangIsoCode($isoCode);
    }

    /**
     * Check for an integer validity (unsigned).
     *
     * @param int $value Integer to validate
     *
     * @return bool
     */
    public function isUnsignedInt($value)
    {
        return ValidateLegacy::isUnsignedInt($value);
    }

    /**
     * Check for a rewritten url validity.
     *
     * @param string $value
     *
     * @return bool
     */
    public function isLinkRewrite($value)
    {
        return ValidateLegacy::isLinkRewrite($value);
    }

    /**
     * Check the given string is a valid object class name
     *
     * @param string $objectClassName object class name
     *
     * @return bool
     */
    public static function isValidObjectClassName(string $objectClassName): bool
    {
        return ValidateLegacy::isValidObjectClassName($objectClassName);
    }
}
