<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Integration\Classes\controller;

use AdminImportController;
use Configuration;
use Country;
use Db;
use Language;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Tests\Integration\Utility\ContextMockerTrait;

class AdminImportControllerTest extends TestCase
{
    use ContextMockerTrait;

    protected function setUp(): void
    {
        parent::setUp();
        self::mockContext();
    }

    public function testACountryNameResolvesToThatCountry(): void
    {
        [$idCountry, $name] = $this->anExistingCountry();

        $this->assertSame($idCountry, $this->resolveCountryId($name));
    }

    public function testAnIsoCodeResolvesToTheSameCountryAsItsName(): void
    {
        [$idCountry, , $isoCode] = $this->anExistingCountry();

        // Without this the ISO code matched no name, and the import fell through to creating a
        // second country called "PL" whose zone is 0, silently detaching the imported addresses
        // from the real one.
        $this->assertSame($idCountry, $this->resolveCountryId($isoCode));
        $this->assertSame($idCountry, $this->resolveCountryId(strtolower($isoCode)));
    }

    public function testResolvingAnIsoCodeCreatesNoCountry(): void
    {
        [, , $isoCode] = $this->anExistingCountry();
        $before = $this->countryCount();

        $this->resolveCountryId($isoCode);

        $this->assertSame($before, $this->countryCount());
    }

    public function testAnUnknownColumnResolvesToNothingWithoutThrowing(): void
    {
        // Country::getByIso() throws on a value that is not an ISO code, and a country column
        // holding a free-text name is the normal case rather than an exceptional one.
        $this->assertSame(0, $this->resolveCountryId('Not A Country At All'));
        $this->assertSame(0, $this->resolveCountryId('ZZ'));
    }

    public function testACountryCreatedTheWayTheImportCreatesOneValidates(): void
    {
        // Mirrors what addressImport()/storeImport() build when the country column matches no
        // existing country. need_identification_number was never set, and it is required, so the
        // creation always failed with "Property Country->need_identification_number is empty."
        // and the row was left with no country at all.
        $country = new Country();
        $country->active = true;
        $country->name = [];
        foreach (Language::getIDs(false) as $idLang) {
            $country->name[$idLang] = 'Import Probe Country';
        }
        $country->id_zone = 0;
        $country->iso_code = 'QQ';
        $country->contains_states = false;

        // Without it the creation branch could never succeed, whatever the country column held.
        $this->assertSame(
            'Property Country->need_identification_number is empty.',
            $country->validateFields(false, true)
        );

        $country->need_identification_number = false;

        $this->assertTrue($country->validateFields(false, true));
        $this->assertTrue($country->validateFieldsLang(false, true));
    }

    /**
     * @return array{0: int, 1: string, 2: string}
     */
    private function anExistingCountry(): array
    {
        $idLang = (int) Configuration::get('PS_LANG_DEFAULT');
        $row = Db::getInstance()->getRow(
            'SELECT c.`id_country`, c.`iso_code`, cl.`name`
            FROM `' . _DB_PREFIX_ . 'country` c
            INNER JOIN `' . _DB_PREFIX_ . 'country_lang` cl
                ON cl.`id_country` = c.`id_country` AND cl.`id_lang` = ' . $idLang . '
            ORDER BY c.`id_country`'
        );
        $this->assertNotEmpty($row, 'the fixture shop has no country to resolve');

        // The name must not itself be the ISO code, or the two lookups cannot be told apart and
        // the ISO assertion would pass through the name branch alone.
        $this->assertNotSame(
            strtoupper($row['name']),
            strtoupper($row['iso_code']),
            'the fixture country is named like its own ISO code'
        );

        return [(int) $row['id_country'], $row['name'], $row['iso_code']];
    }

    private function countryCount(): int
    {
        return (int) Db::getInstance()->getValue('SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'country`');
    }

    private function resolveCountryId(string $field): int
    {
        $method = new ReflectionMethod(AdminImportController::class, 'resolveCountryId');
        $method->setAccessible(true);

        return (int) $method->invoke(null, $field);
    }
}
