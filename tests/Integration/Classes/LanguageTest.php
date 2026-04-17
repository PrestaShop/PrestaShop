<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Language;
use PHPUnit\Framework\TestCase;
use Tests\Resources\DatabaseDump;

class LanguageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        DatabaseDump::restoreAllTables();
    }

    public function testGetIdByIso()
    {
        $this->assertNull(Language::getIdByIso('zz', false));
        $this->assertNull(Language::getIdByIso('zz', true));

        $language = new Language();
        $language->name = 'zz';
        $language->iso_code = 'zz';
        $language->locale = 'zz-ZZ';
        $language->language_code = 'zz-ZZ';
        $language->add();

        $idByIso = Language::getIdByIso('zz', false);
        $this->assertNotEquals(0, $idByIso);
        $this->assertIsInt($idByIso);

        $idByIso = Language::getIdByIso('zz', true);
        $this->assertNotEquals(0, $idByIso);
        $this->assertIsInt($idByIso);
    }
}
