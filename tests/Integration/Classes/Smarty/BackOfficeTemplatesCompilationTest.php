<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\Smarty;

use PHPUnit\Framework\TestCase;

class BackOfficeTemplatesCompilationTest extends TestCase
{
    private const TEMPLATES_DIR = _PS_ROOT_DIR_ . '/admin-dev/themes/default/template/';

    /**
     * @dataProvider getTemplates
     */
    public function testTemplateDoesNotUseUnregisteredFunctions(string $template): void
    {
        global $smarty;
        $smarty->force_compile = true;

        $deprecations = [];
        set_error_handler(static function (int $type, string $message) use (&$deprecations): bool {
            if (str_contains($message, 'unregistered function')) {
                $deprecations[] = $message;
            }

            return true;
        }, E_USER_DEPRECATED);

        try {
            $smarty->createTemplate('file:' . self::TEMPLATES_DIR . $template)->compileTemplateSource();
        } finally {
            restore_error_handler();
        }

        $this->assertSame([], $deprecations);
    }

    public static function getTemplates(): iterable
    {
        yield 'tax rules list' => ['controllers/tax_rules/helpers/list/list_content.tpl'];
        yield 'calendar helper' => ['helpers/calendar/calendar.tpl'];
        yield 'form helper' => ['helpers/form/form.tpl'];
        yield 'ajax layout' => ['layout-ajax.tpl'];
    }
}
