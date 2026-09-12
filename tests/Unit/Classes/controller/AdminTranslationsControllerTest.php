<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Classes\Controller;

use AdminTranslationsControllerCore;
use Language;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class AdminTranslationsControllerTest extends TestCase
{
    /**
     * @dataProvider mailFilesProvider
     */
    public function testDisplayMailContentSupportsMissingMailFormat(array $mailFiles): void
    {
        $language = (new ReflectionClass(Language::class))->newInstanceWithoutConstructor();
        $language->iso_code = 'en';

        $content = (new TestAdminTranslationsController())->displayMailContentForTest(
            [
                'empty_values' => 0,
                'total_filled' => 1,
                'directory' => _PS_ROOT_DIR_ . '/mails/',
                'files' => ['test_mail' => $mailFiles],
            ],
            [],
            $language
        );

        $this->assertStringContainsString('name="mail[html][test_mail]"', $content);
        $this->assertStringContainsString('name="mail[txt][test_mail]"', $content);
    }

    public static function mailFilesProvider(): iterable
    {
        yield 'missing HTML template' => [
            ['txt' => ['en' => 'Text content']],
        ];

        yield 'missing TXT template' => [
            ['html' => ['en' => '<html><body>HTML content</body></html>']],
        ];
    }
}

class TestAdminTranslationsController extends AdminTranslationsControllerCore
{
    public function __construct()
    {
    }

    public function displayMailContentForTest(array $mails, array $subjects, Language $language): string
    {
        return parent::displayMailContent($mails, $subjects, $language, 'mail-content', 'Mail content');
    }

    protected function trans($id, array $parameters = [], $domain = null, $locale = null)
    {
        return strtr($id, $parameters);
    }
}
