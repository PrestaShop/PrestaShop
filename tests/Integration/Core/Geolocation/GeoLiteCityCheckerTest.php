<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\Geolocation;

use PrestaShop\PrestaShop\Core\Geolocation\GeoLite\GeoLiteCityCheckerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * The geolocation screen tells a merchant where to put the MaxMind data, and the availability check
 * is an exact directory-plus-filename match. These pin the two together, because a message that does
 * not name the file leaves the merchant guessing and the screen keeps showing the same warning.
 */
class GeoLiteCityCheckerTest extends KernelTestCase
{
    private const TEMPLATE = '/src/PrestaShopBundle/Resources/views/Admin/Improve/International/Geolocation/index.html.twig';

    private ?string $createdFile = null;

    protected function tearDown(): void
    {
        if (null !== $this->createdFile && file_exists($this->createdFile)) {
            unlink($this->createdFile);
        }
        $this->createdFile = null;

        parent::tearDown();
    }

    public function testTheCheckLooksForExactlyTheDeclaredFileInTheDeclaredDirectory(): void
    {
        self::bootKernel();
        global $kernel;
        $kernel = self::$kernel;

        /** @var GeoLiteCityCheckerInterface $checker */
        $checker = self::getContainer()->get('prestashop.core.geolocation.geo_lite_city.checker');

        $expected = _PS_GEOIP_DIR_ . _PS_GEOIP_CITY_FILE_;

        // Control: without the file the screen must consider the database unavailable, otherwise the
        // assertion below would be true for the wrong reason.
        $this->assertFileDoesNotExist($expected, 'A GeoLite database is already installed, so this test cannot say anything.');
        $this->assertFalse($checker->isAvailable());

        $this->createdFile = $expected;
        file_put_contents($expected, 'not a real database');

        $this->assertTrue($checker->isAvailable(), 'The check did not accept the file at the path it declares.');
    }

    public function testTheWarningNamesTheFileTheCheckRequires(): void
    {
        $template = file_get_contents(_PS_ROOT_DIR_ . self::TEMPLATE);

        // Vacuity guard: if the template could not be read, or no longer carries the warning at all,
        // the assertion below would be meaningless rather than false.
        $this->assertIsString($template);
        $this->assertStringContainsString('geolocationDatabaseAvailable', $template, 'Failed to read the geolocation screen.');
        $this->assertStringContainsString('MaxMind', $template);

        $this->assertStringContainsString(
            "constant('_PS_GEOIP_CITY_FILE_')",
            $template,
            'The warning no longer takes the file name from the constant the availability check uses, so the two can drift apart.'
        );
    }
}
