<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\MailTemplate;

use PHPUnit\Framework\TestCase;

/**
 * A core mail is only usable if every shipped theme can render it. Themes are discovered by scanning
 * folders, so a template added to one theme and forgotten in the other produces no error at all: the
 * mail simply cannot be generated for a shop using the other theme.
 */
class CoreMailThemeParityTest extends TestCase
{
    private const THEMES_DIR = _PS_ROOT_DIR_ . '/mails/themes';

    public function testEveryCoreMailTemplateExistsInEveryTheme(): void
    {
        $templatesByTheme = $this->getCoreTemplatesByTheme();

        self::assertGreaterThan(1, count($templatesByTheme), 'Expected more than one mail theme to compare.');

        $allTemplates = array_unique(array_merge(...array_values($templatesByTheme)));
        sort($allTemplates);

        self::assertNotEmpty($allTemplates, 'No core mail templates were found, the test would pass vacuously.');

        foreach ($templatesByTheme as $theme => $templates) {
            self::assertSame(
                $allTemplates,
                $templates,
                sprintf('Mail theme "%s" is missing core templates that another theme provides.', $theme)
            );
        }
    }

    /**
     * @return array<string, string[]>
     */
    private function getCoreTemplatesByTheme(): array
    {
        $templatesByTheme = [];

        foreach (glob(self::THEMES_DIR . '/*', GLOB_ONLYDIR) ?: [] as $themeDir) {
            $coreDir = $themeDir . '/core';
            if (!is_dir($coreDir)) {
                continue;
            }

            $templates = array_map(
                'basename',
                glob($coreDir . '/*.html.twig') ?: []
            );
            sort($templates);

            $templatesByTheme[basename($themeDir)] = $templates;
        }

        return $templatesByTheme;
    }
}
