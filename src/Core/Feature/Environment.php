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


namespace PrestaShop\PrestaShop\Core\Feature;

/**
 * Disable Symfony toolbar
 */
final class Environment
{
    const ENV_VAR = '_APP_ENV_';

    /**
     * @param string $defaultValue The default environment name.
     *
     * @return string
     */
    public static function getEnvironment($defaultEnvironment)
    {
        $fromEnv = getenv(self::ENV_VAR);

        return null === $fromEnv ? $defaultEnvironment : $fromEnv;
    }

    /**
     * @param bool $defaultMode The default Debug mode.
     *
     * @return bool
     */
    public static function getDebugMode($defaultMode)
    {
        $fromEnv = getenv(self::ENV_VAR);

        if (null !== $fromEnv)  {
            return $fromEnv !== 'prod';
        }

        return $defaultMode;
    }
}
