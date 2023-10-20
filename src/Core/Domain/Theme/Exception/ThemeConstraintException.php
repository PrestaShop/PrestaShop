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

namespace PrestaShop\PrestaShop\Core\Domain\Theme\Exception;

/**
 * Thrown when theme constraints are violated
 */
class ThemeConstraintException extends ThemeException
{
    /**
     * When trying to change theme in multi-shop context
     */
    public const RESTRICTED_ONLY_FOR_SINGLE_SHOP = 1;

    /**
     * When trying to upload zip file which does not contain theme.yml configuration file.
     */
    public const MISSING_CONFIGURATION_FILE = 2;

    /**
     * Its either theme has missing required files or some required properties in theme.yml
     */
    public const INVALID_CONFIGURATION = 3;

    /**
     * Some mandatory files are missing.
     */
    public const INVALID_DATA = 4;
}
