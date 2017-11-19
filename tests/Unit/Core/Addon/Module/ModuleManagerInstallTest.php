<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Core\Addon\Module;

use PHPUnit\Framework\TestCase;

class ModuleManagerInstallTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
    }


    public function test_data_in_database_after_module_install()
    {
    }

    public function test_successful_install_with_zip()
    {
    }

    public function test_failed_install_caused_by_parse_error()
    {
    }

    public function test_installation_paypal_module_from_addons()
    {
    }

    public function test_failed_install_caused_by_response_from_module()
    {
        // Should uninstall
    }
}
