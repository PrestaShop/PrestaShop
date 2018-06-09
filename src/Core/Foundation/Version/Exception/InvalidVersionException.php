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
namespace PrestaShop\PrestaShop\Core\Foundation\Version\Exception;

use Exception;

/**
 * This exception will be thrown ff an invalid Shop version name is used
 * in the application.
 */
class InvalidVersionException extends Exception
{
    /**
     * Creates an exception for the invalid type.
     *
     * @return static The created exception.
     */
    public static function mustBeAString()
    {
        return new static('A valid version must be a string.');
    }

    /**
     * Creates an exception for the invalid version name.
     *
     * @param string  $versionName  The version name.
     *
     * @return static The created exception.
     */
    public static function mustBeValidName($versionName)
    {
        return new static(sprintf(
            'You provided an invalid version string ("%s"). A valid version string must contain four numeric characters divided by three "." characters, for example "1.7.4.0".',
            $versionName
        ));
    }
}
