<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace LegacyTests\Unit\Classes;

use PHPUnit\Framework\TestCase;
use Profile;

class ProfileCoreTest extends TestCase
{
    private $accessType = [
        'view',
        'add',
        'edit',
        'delete',
    ];

    public function testGetAccess()
    {
        $idProfile = 2;
        foreach (Profile::getProfileAccesses($idProfile, 'id_tab') as $tab) {
            /*
            Expected:
            Array &13 (
                'id_tab' => '5'
                'class_name' => 'AdminInvoices'
                'id_profile' => 2
                'view' => '1'
                'add' => '1'
                'edit' => '1'
                'delete' => '1'
            )
            */

            $this->assertTrue(is_array($tab));

            $this->assertArrayHasKey('id_tab', $tab);
            $this->assertFalse(empty($tab['class_name']));
            $this->assertSame($idProfile, $tab['id_profile']);

            // For each access type, we expect "granted" or "refused" boolean values
            foreach ($this->accessType as $type) {
                $this->assertArrayHasKey($type, $tab);
                $this->assertTrue(in_array($tab[$type], ['0', '1']));
            }
        }
    }
}
