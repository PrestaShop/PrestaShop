<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use AdminTranslationsController;
use PHPUnit\Framework\TestCase;
use PrestaShopException;
use ReflectionClass;
use ReflectionMethod;
use Validate;

/**
 * When saving e-mail translations, the template name comes from the submitted array key
 * (core_mail[tpl][<name>] / module_mail[tpl][<name>]) and is used to build the destination
 * file path. That name must always be checked with Validate::isTplName(), so a value such as
 * "../../themes/pwn" cannot write a file outside the mails directory. The check used to sit
 * inside the "name contains a module prefix" branch, so a key with no pipe (or a pipe at the
 * first position) skipped it entirely.
 */
class AdminTranslationsMailNameTest extends TestCase
{
    /**
     * @dataProvider invalidMailNameProvider
     */
    public function testMailNameEscapingTheMailsDirectoryIsRejected(string $mailName): void
    {
        // Guard the premise: isTplName itself must reject the traversal name.
        self::assertSame(0, (int) Validate::isTplName($mailName));

        $controller = (new ReflectionClass(AdminTranslationsController::class))->newInstanceWithoutConstructor();

        // Minimal state read by submitTranslationsMails before the name is validated.
        $translator = new class() {
            public function trans($id, array $parameters = [], $domain = null, $locale = null): string
            {
                return (string) $id;
            }
        };
        $this->setProperty($controller, 'translator', $translator);
        $this->setProperty($controller, 'theme_selected', false);
        $this->setProperty($controller, 'type_selected', 'mails');
        $this->setProperty($controller, 'translations_informations', [
            'mails' => ['dir' => sys_get_temp_dir() . '/ps-mail-tpl-' . uniqid() . '/'],
        ]);

        $_POST['core_mail'] = ['tpl' => [$mailName => 'proof']];
        unset($_POST['module_mail'], $_POST['subject'], $_GET['module_mail']);

        $method = new ReflectionMethod($controller, 'submitTranslationsMails');
        $method->setAccessible(true);

        try {
            $this->expectException(PrestaShopException::class);
            $method->invoke($controller);
        } finally {
            unset($_POST['core_mail']);
        }
    }

    public static function invalidMailNameProvider(): array
    {
        return [
            'traversal, no pipe' => ['../../themes/mailtpl'],
            'traversal, pipe at first position' => ['|../../themes/mailtpl'],
        ];
    }

    private function setProperty(object $object, string $name, $value): void
    {
        $property = (new ReflectionClass($object))->getProperty($name);
        $property->setAccessible(true);
        $property->setValue($object, $value);
    }
}
