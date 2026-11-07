<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Email;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Email\LegacyEmailTemplateLister;
use Symfony\Component\Filesystem\Filesystem;

/**
 * A legacy mail template is a .html file, a .txt file, or both: which one is required depends on
 * PS_MAIL_TYPE, and Mail::getTemplateBasePath() accepts a template as soon as either exists.
 */
class LegacyEmailTemplateListerTest extends TestCase
{
    private string $rootDir;
    private string $mailsDir;
    private string $modulesDir;
    private Filesystem $filesystem;

    protected function setUp(): void
    {
        parent::setUp();

        $this->filesystem = new Filesystem();
        $this->rootDir = sys_get_temp_dir() . '/legacy_email_lister_' . uniqid('', true);
        $this->mailsDir = $this->rootDir . '/mails';
        $this->modulesDir = $this->rootDir . '/modules';
        $this->filesystem->mkdir([$this->mailsDir . '/en', $this->modulesDir . '/probemodule/mails/en']);
    }

    protected function tearDown(): void
    {
        $this->filesystem->remove($this->rootDir);
        parent::tearDown();
    }

    /**
     * @dataProvider provideTemplateShapes
     *
     * @param string[] $files
     */
    public function testATemplateIsListedWhenEitherHalfExists(array $files, bool $expectHtml, bool $expectTxt): void
    {
        foreach ($files as $file) {
            $this->filesystem->dumpFile($this->mailsDir . '/en/' . $file, 'content');
        }

        $templates = $this->createLister()->getLegacyTemplates('en');

        $this->assertArrayHasKey('order_conf', $templates, 'The template was dropped from the listing.');
        $this->assertSame($expectHtml, null !== $templates['order_conf']['html_path']);
        $this->assertSame($expectTxt, null !== $templates['order_conf']['txt_path']);
        $this->assertTrue($templates['order_conf']['is_core']);
        $this->assertSame('', $templates['order_conf']['module']);
    }

    /**
     * @return array<string, array{string[], bool, bool}>
     */
    public static function provideTemplateShapes(): array
    {
        return [
            'both halves' => [['order_conf.html', 'order_conf.txt'], true, true],
            // PS_MAIL_TYPE = TYPE_HTML or TYPE_BOTH_AUTOMATIC_TEXT sends these without a .txt.
            'html only' => [['order_conf.html'], true, false],
            'txt only' => [['order_conf.txt'], false, true],
        ];
    }

    public function testAModuleTemplateWithOnlyOneHalfIsListed(): void
    {
        $this->filesystem->dumpFile($this->modulesDir . '/probemodule/mails/en/module_alert.html', 'content');

        $templates = $this->createLister()->getLegacyTemplates('en');

        $this->assertArrayHasKey('module_alert', $templates);
        $this->assertSame('probemodule', $templates['module_alert']['module']);
        $this->assertFalse($templates['module_alert']['is_core']);
        $this->assertNull($templates['module_alert']['txt_path']);
    }

    public function testEachTemplateIsReportedOnceWhateverTheFileOrder(): void
    {
        $this->filesystem->dumpFile($this->mailsDir . '/en/order_conf.html', 'content');
        $this->filesystem->dumpFile($this->mailsDir . '/en/order_conf.txt', 'content');
        $this->filesystem->dumpFile($this->mailsDir . '/en/account.txt', 'content');

        $templates = $this->createLister()->getLegacyTemplates('en');

        $this->assertCount(2, $templates);
        $this->assertNotNull($templates['order_conf']['html_path']);
        $this->assertNotNull($templates['order_conf']['txt_path']);
        $this->assertNull($templates['account']['html_path']);
    }

    public function testACoreTemplateSurvivesAModuleShippingTheSameName(): void
    {
        // ps_emailalerts really does ship order_changed and productoutofstock beside the core ones.
        $this->filesystem->dumpFile($this->mailsDir . '/en/order_changed.html', 'core');
        $this->filesystem->dumpFile($this->mailsDir . '/en/order_changed.txt', 'core');
        // Both halves, so the module entry survives the scan and really does reach the merge.
        $this->filesystem->dumpFile($this->modulesDir . '/probemodule/mails/en/order_changed.html', 'module');
        $this->filesystem->dumpFile($this->modulesDir . '/probemodule/mails/en/order_changed.txt', 'module');

        $templates = $this->createLister()->getLegacyTemplates('en');

        $this->assertTrue($templates['order_changed']['is_core'], 'A module overwrote the core template.');
        $this->assertSame('', $templates['order_changed']['module']);
        $this->assertStringStartsWith($this->mailsDir, (string) $templates['order_changed']['html_path']);
    }

    public function testAMissingLanguageDirectoryYieldsNoTemplates(): void
    {
        $this->assertSame([], $this->createLister()->getLegacyTemplates('zz'));
    }

    private function createLister(): LegacyEmailTemplateLister
    {
        return new LegacyEmailTemplateLister($this->mailsDir, $this->modulesDir);
    }
}
