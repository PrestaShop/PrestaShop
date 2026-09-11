<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Twig;

use PHPUnit\Framework\TestCase;

/**
 * A hook a back office template renders has to be declared in the install data as well, or a fresh
 * shop never creates the row for it and the hook does not appear where a merchant positions modules.
 */
class RenderHookDeclarationTest extends TestCase
{
    private const VIEWS_DIRECTORY = '/src/PrestaShopBundle/Resources/views';
    private const HOOK_FILE = '/install-dev/data/xml/hook.xml';

    public function testEveryHookRenderedByATemplateIsDeclaredInTheInstallData(): void
    {
        $declared = $this->getDeclaredHookNames();
        $this->assertNotEmpty($declared, 'No hook is declared in ' . self::HOOK_FILE . '.');

        $rendered = $this->getRenderedHookNames();
        $this->assertNotEmpty($rendered, 'No template renders a hook, so this test would pass vacuously.');

        $this->assertSame([], array_values(array_diff($rendered, $declared)));
    }

    /**
     * @return string[]
     */
    private function getDeclaredHookNames(): array
    {
        $hooks = simplexml_load_file(_PS_ROOT_DIR_ . self::HOOK_FILE);
        $this->assertNotFalse($hooks, self::HOOK_FILE . ' cannot be parsed.');

        $names = [];
        foreach ($hooks->entities->hook as $hook) {
            $names[] = (string) $hook->name;
        }

        return $names;
    }

    /**
     * @return string[]
     */
    private function getRenderedHookNames(): array
    {
        $directory = new \RecursiveDirectoryIterator(_PS_ROOT_DIR_ . self::VIEWS_DIRECTORY);
        $names = [];

        /** @var \SplFileInfo $file */
        foreach (new \RecursiveIteratorIterator($directory) as $file) {
            if ($file->getExtension() !== 'twig') {
                continue;
            }

            $template = file_get_contents($file->getPathname());
            if ($template === false) {
                continue;
            }

            // A hook name built from a variable cannot be checked here and is left out on purpose.
            preg_match_all('/renderhook(?:s?Array)?\(\s*[\'"]([A-Za-z0-9_]+)[\'"]/', $template, $matches);
            $names = array_merge($names, $matches[1]);
        }

        return array_values(array_unique($names));
    }
}
