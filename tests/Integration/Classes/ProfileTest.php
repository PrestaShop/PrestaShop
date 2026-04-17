<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Test\Integration\Classes;

use PHPUnit\Framework\TestCase;
use Profile;

class ProfileTest extends TestCase
{
    public function testGetAccess(): void
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
            foreach ([
                'view',
                'add',
                'edit',
                'delete',
            ] as $type) {
                $this->assertArrayHasKey($type, $tab);
                $this->assertTrue(in_array($tab[$type], ['0', '1']));
            }
        }
    }
}
