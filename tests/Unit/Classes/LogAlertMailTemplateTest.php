<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Classes;

use PHPUnit\Framework\TestCase;

/**
 * PrestaShopLogger::sendByMail() sends log_alert with an empty template variable list:
 *
 *     Mail::Send(
 *         (int) Configuration::get('PS_LANG_DEFAULT'),
 *         'log_alert',
 *         ... ,
 *         [],          // no variables
 *         $to
 *     );
 *
 * Nothing can substitute a placeholder in that template, so any {placeholder} it contains reaches the
 * merchant's inbox verbatim. The modern theme was changed to a plain greeting when this was reported;
 * the classic theme kept "Hi {firstname} {lastname}," and still showed it literally.
 */
class LogAlertMailTemplateTest extends TestCase
{
    /**
     * @dataProvider getLogAlertTemplates
     */
    public function testTheTemplateUsesNoVariableTheSenderCannotProvide(string $templatePath): void
    {
        $contents = file_get_contents($templatePath);
        self::assertIsString($contents, sprintf('%s could not be read', $templatePath));

        preg_match_all('/\{[a-z][a-z0-9_]*\}/', $contents, $matches);

        self::assertSame(
            [],
            array_values(array_unique($matches[0])),
            sprintf(
                '%s uses mail placeholders, but PrestaShopLogger::sendByMail() passes no variables, '
                . 'so they are delivered literally.',
                str_replace(_PS_ROOT_DIR_ . '/', '', $templatePath)
            )
        );
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function getLogAlertTemplates(): iterable
    {
        foreach (glob(_PS_ROOT_DIR_ . '/mails/themes/*/core/log_alert.html.twig') ?: [] as $templatePath) {
            $theme = basename(dirname($templatePath, 2));

            yield $theme => [$templatePath];
        }
    }

    /**
     * Guards the provider itself: an empty glob would make every case above vacuously pass.
     */
    public function testEveryShippedThemeIsCovered(): void
    {
        $themes = array_keys(iterator_to_array(self::getLogAlertTemplates()));

        self::assertContains('classic', $themes);
        self::assertContains('modern', $themes);
    }
}
