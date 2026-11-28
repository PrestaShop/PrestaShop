<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\Pdf;

use Configuration;
use HTMLTemplate;
use Shop;
use Smarty;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DatabaseDump;

/**
 * The PDF header handed the renderer an http(s) URL for the shop logo, so producing an invoice made the
 * shop fetch its own file back over the network. On an installation that cannot reach its own public
 * address - a non-default port, split DNS, a container behind a proxy - the logo silently went missing.
 */
class HTMLTemplateLogoPathTest extends KernelTestCase
{
    private const TABLES_TO_RESTORE = ['configuration', 'configuration_lang'];
    private const LOGO_FILENAME = 'ps-pdf-logo-path-test.png';

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        DatabaseDump::restoreTables(self::TABLES_TO_RESTORE);

        // getLogo() only answers for a file that exists, and getimagesize() has to read it, so the test
        // brings its own rather than depending on what the fixture image directory happens to ship.
        file_put_contents(
            _PS_IMG_DIR_ . self::LOGO_FILENAME,
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==')
        );
    }

    protected function tearDown(): void
    {
        @unlink(_PS_IMG_DIR_ . self::LOGO_FILENAME);
        DatabaseDump::restoreTables(self::TABLES_TO_RESTORE);
        parent::tearDown();
    }

    public function testTheLogoIsHandedToTheRendererAsAReadableFile(): void
    {
        $shopId = (int) Configuration::get('PS_SHOP_DEFAULT');
        Configuration::updateValue('PS_LOGO', self::LOGO_FILENAME, false, null, $shopId);

        $template = $this->buildTemplate();
        $template->assignCommonHeaderData();

        $logoPath = $template->assigned['logo_path'];

        $this->assertNotSame('', $logoPath, 'The fixture shop needs a configured logo.');
        // The whole point: no scheme, and a file the renderer can open without a network round trip.
        $this->assertDoesNotMatchRegularExpression('#^https?://#', $logoPath);
        $this->assertFileExists($logoPath);
        // And it is still the configured logo, not some other file.
        $this->assertStringEndsWith(
            (string) Configuration::get('PS_LOGO', null, null, (int) $template->shop->id),
            $logoPath
        );
    }

    public function testNoConfiguredLogoYieldsAnEmptyPathRatherThanTheImageDirectory(): void
    {
        $shopId = (int) Configuration::get('PS_SHOP_DEFAULT');
        Configuration::updateValue('PS_LOGO', '', false, null, $shopId);
        Configuration::updateValue('PS_LOGO_INVOICE', '', false, null, $shopId);

        $template = $this->buildTemplate();
        $template->assignCommonHeaderData();

        // header.tpl renders the <img> on {if $logo_path}: a bare directory is truthy and would draw a
        // broken image, so the absence of a logo has to read as absent.
        $this->assertSame('', $template->assigned['logo_path']);
    }

    private function buildTemplate(): HTMLTemplateLogoPathTestDouble
    {
        $template = new HTMLTemplateLogoPathTestDouble();
        $template->smarty = new HTMLTemplateLogoPathSmartyRecorder($template);
        $template->shop = new Shop((int) Configuration::get('PS_SHOP_DEFAULT'));

        return $template;
    }
}

/**
 * HTMLTemplate is abstract and only its header assignment is under test here.
 */
class HTMLTemplateLogoPathTestDouble extends HTMLTemplate
{
    /** @var array<string, mixed> */
    public $assigned = [];

    public function getContent()
    {
        return '';
    }

    public function getFilename()
    {
        return '';
    }

    public function getBulkFilename()
    {
        return '';
    }
}

/**
 * Captures what the template hands to Smarty, which is what the renderer will read.
 */
class HTMLTemplateLogoPathSmartyRecorder extends Smarty
{
    public function __construct(private HTMLTemplateLogoPathTestDouble $template)
    {
        parent::__construct();
    }

    public function assign($tpl_var, $value = null, $nocache = false)
    {
        $variables = is_array($tpl_var) ? $tpl_var : [$tpl_var => $value];
        $this->template->assigned = array_merge($this->template->assigned, $variables);

        return $this;
    }
}
