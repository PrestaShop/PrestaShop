<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Test\Integration\Classes;

use Access;
use Language;
use PHPUnit\Framework\TestCase;
use Profile;
use Tab;
use Tests\Resources\DatabaseDump;

class ProfileTest extends TestCase
{
    private const TOUCHED_TABLES = ['tab', 'tab_lang', 'authorization_role', 'access'];

    protected function setUp(): void
    {
        parent::setUp();

        DatabaseDump::restoreTables(self::TOUCHED_TABLES);
        Profile::resetStaticCache();
    }

    protected function tearDown(): void
    {
        DatabaseDump::restoreTables(self::TOUCHED_TABLES);
        Profile::resetStaticCache();

        parent::tearDown();
    }

    /**
     * ModuleTabRegister duplicates a lone parent tab under <ParentClass>_MTR, so a class name with an
     * underscore is something PrestaShop produces by itself. The permission slugs of such a tab must be
     * reported against that tab and not against the class name that precedes the underscore.
     *
     * @see https://github.com/PrestaShop/PrestaShop/issues/28308
     */
    public function testAccessesOfATabWhoseClassNameCarriesAnUnderscoreAreReportedAgainstThatTab(): void
    {
        $idProfile = 2;
        $className = 'AdminProbeParent_MTR';

        $names = [];
        foreach (Language::getIDs(false) as $idLang) {
            $names[(int) $idLang] = 'Probe parent MTR';
        }

        $tab = new Tab();
        $tab->class_name = $className;
        $tab->id_parent = 0;
        $tab->module = '';
        $tab->active = true;
        $tab->name = $names;
        $this->assertTrue($tab->add());

        $access = new Access();
        foreach (['view', 'add', 'edit', 'delete'] as $action) {
            $access->updateLgcAccess($idProfile, (int) $tab->id, $action, true);
        }

        Profile::resetStaticCache();
        $accesses = Profile::getProfileAccesses($idProfile, 'class_name');

        $this->assertArrayHasKey($className, $accesses);
        foreach (['view', 'add', 'edit', 'delete'] as $action) {
            $this->assertSame('1', $accesses[$className][$action], sprintf('%s on %s', $action, $className));
        }
    }

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
