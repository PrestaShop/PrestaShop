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

namespace PrestaShop\PrestaShop\Core\Security;

class Permission
{
    public const PREFIX_TAB = 'ROLE_MOD_TAB_';
    public const PREFIX_MODULE = 'ROLE_MOD_MODULE_';

    public const CREATE = 'create';
    public const UPDATE = 'update';
    public const DELETE = 'delete';
    public const READ = 'read';

    public const LEVEL_READ = 1;
    public const LEVEL_UPDATE = 2;
    public const LEVEL_CREATE = 3;
    public const LEVEL_DELETE = 4;

    // This class should not be instanciated
    private function __construct()
    {
    }
}
