<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Twig;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Twig\TemplateIterator;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;

class TemplateIteratorTest extends TestCase
{
    private const FILES = [
        'admin/page.html.twig',
        'admin/form.html',
        'admin/script.js',
        'admin/vendor/lib/page.html.twig',
        'modules/mymodule/mymodule.php',
        'modules/mymodule/config.xml',
        'modules/mymodule/mails/en/order_conf.html',
        'modules/mymodule/data/labels.csv',
        'modules/mymodule/vendor/package/template.html.twig',
        'modules/mymodule/node_modules/package/index.html',
        'modules/mymodule/standalone.html.twig',
        'modules/mymodule/views/templates/admin/configure.html.twig',
        'modules/mymodule/views/templates/dist/index.html',
        'modules/mymodule/views/templates/hook/display.tpl',
        'modules/mymodule/views/templates/admin/notes.txt',
        'modules/mymodule/views/templates/admin/icon.svg',
        'modules/mymodule/views/img/logo.png',
        'modules/mymodule/views/css/front.html',
        'modules/mymodule/views/js/app.html',
    ];

    private string $rootDir;

    protected function setUp(): void
    {
        $this->rootDir = sys_get_temp_dir() . '/' . uniqid('ps_twig_template_iterator_');
        $filesystem = new Filesystem();
        foreach (self::FILES as $file) {
            $filesystem->dumpFile($this->rootDir . '/' . $file, '');
        }
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->rootDir);
    }

    public function testListsOnlyTemplates(): void
    {
        $kernel = $this->createMock(KernelInterface::class);
        $kernel->method('getBundles')->willReturn([]);

        $iterator = new TemplateIterator($kernel, [
            $this->rootDir . '/admin' => 'Admin',
            $this->rootDir . '/modules' => TemplateIterator::MODULES_NAMESPACE,
        ]);
        $templates = iterator_to_array($iterator);
        sort($templates);

        $this->assertSame([
            '@Admin/form.html',
            '@Admin/page.html.twig',
            '@Modules/mymodule/standalone.html.twig',
            '@Modules/mymodule/views/templates/admin/configure.html.twig',
            '@Modules/mymodule/views/templates/dist/index.html',
        ], $templates);
    }
}
