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

namespace PrestaShop\PrestaShop\Core\Domain\Language\Exception;

/**
 * Is thrown when error that is related to default language occurs
 */
class DefaultLanguageException extends LanguageException
{
    /**
     * @var string Code is used when deleting default language
     */
    public const CANNOT_DELETE_ERROR = 1;

    /**
     * @var string Code is used when disabling default language
     */
    public const CANNOT_DISABLE_ERROR = 2;

    /**
     * @var string Code is used when deleting language that is use (e.g. as employee's default language)
     */
    public const CANNOT_DELETE_IN_USE_ERROR = 3;

    /**
     * @var string Code is used when deleting default language
     */
    public const CANNOT_DELETE_DEFAULT_ERROR = 4;
}
